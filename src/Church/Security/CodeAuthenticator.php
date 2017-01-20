<?php

namespace Church\Security;

use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use Symfony\Component\HttpFoundation\Response;

use Church\Entity\User\User;

class CodeAuthenticator implements
    SimplePreAuthenticatorInterface,
    AuthenticationFailureHandlerInterface
{

    protected $doctrine;

    protected $http;

    public function __construct(Doctrine $doctrine, HttpUtils $http)
    {
        $this->doctrine = $doctrine;
        $this->http = $http;
    }

    public function createToken(Request $request, $providerKey)
    {

        $http = $this->getHTTP();

        // Only Create a Token when on a verify path and get the type.
        if ($http->checkRequestPath($request, 'user_verify_email')) {
            $type = 'email';
        } elseif ($http->checkRequestPath($request, 'user_verify_phone')) {
            $type = 'phone';
        } else {
            return;
        }

        // Look for a Token & Code in the URL.
        $token = $request->get('token');
        $code = $request->get('code');

        if (!$token || !$code) {
            throw new BadCredentialsException('No Token or Code Found');
        }

        $credentials = array(
            'token' => $token,
            'code' => $code,
            'type' => $type,
        );

        return new PreAuthenticatedToken('anon.', $credentials, $providerKey);
    }

    public function authenticateToken(
        TokenInterface $token,
        UserProviderInterface $userProvider,
        $providerKey
    ) {

        $credentials = $token->getCredentials();
        $type = $credentials['type'];
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();

        if ($type == 'email') {
            $repository = $doctrine->getRepository('Church:User\EmailVerify');
        } elseif ($type == 'phone') {
            $repository = $doctrine->getRepository('Church:User\PhoneVerify');
        }

        // User is already in the session.
        $user = $token->getUser();
        if ($user instanceof User) {
            return new PreAuthenticatedToken($user, $credentials, $providerKey, $user->getRoles());
        }


        if ($verify = $repository->findOneByToken($credentials['token'])) {
            $created = clone $verify->getCreated();
            $created->add(new \DateInterval('PT1H'));

            $now = new \DateTime('now');

            if ($created < $now) {
                throw new AuthenticationException('Verification Code is older than 1 hour');
            }

            if ($verify->getCode() != $credentials['code']) {
                throw new AuthenticationException('Token & Verification Code do not match');
            }
        } else {
            throw new AuthenticationException('Token does not exist');
        }

        if ($type == 'email') {
            $user = $verify->getEmail()->getUser();
        } elseif ($type == 'phone') {
            $user = $verify->getPhone()->getUser();
        }

        return new PreAuthenticatedToken($user, $credentials, $providerKey, $user->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        $http = $this->getHTTP();
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();

        if ($http->checkRequestPath($request, 'user_verify_email')) {
            $repository = $doctrine->getRepository('Church:User\EmailVerify');
        } elseif ($http->checkRequestPath($request, 'user_verify_phone')) {
            $repository = $doctrine->getRepository('Church:User\PhoneVerify');
        }

        if ($verify = $request->get('token')) {
            $em->remove($verify);
            $em->flush();
        }

        return new Response("Authentication Failed.", 403);
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    public function getHTTP()
    {
        return $this->http;
    }
}
