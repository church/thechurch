<?php

namespace Church\Security;

use Church\Entity\User\User;
use Church\Entity\User\PhoneVerify;
use Church\Entity\User\EmailVerify;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Code Authenticator.
 */

class CodeAuthenticator implements
    SimplePreAuthenticatorInterface,
    AuthenticationFailureHandlerInterface
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var HttpUtils
     */
    protected $http;

    /**
     * Creates a new Code Authenticator.
     *
     * @param RegistryInterface $doctrine
     * @param HttpUtils $http
     */
    public function __construct(RegistryInterface $doctrine, HttpUtils $http)
    {
        $this->doctrine = $doctrine;
        $this->http = $http;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        // Only Create a Token when on a verify path and get the type.
        // @TODO Update these paths.
        if ($this->http->checkRequestPath($request, 'user_verify_email')) {
            $type = 'email';
        } elseif ($this->http->checkRequestPath($request, 'user_verify_phone')) {
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

        $credentials = [
            'token' => $token,
            'code' => $code,
            'type' => $type,
        ];

        return new PreAuthenticatedToken('anon.', $credentials, $providerKey);
    }

    /**
     * {@inheritdoc}
     */
    public function authenticateToken(
        TokenInterface $token,
        UserProviderInterface $userProvider,
        $providerKey
    ) {

        $credentials = $token->getCredentials();
        $type = $credentials['type'];
        $doctrine = $this->getDoctrine();
        $em = $doctrine->getManager();

        switch ($type) {
            case 'email':
                $repository = $doctrine->getRepository(EmailVerify::class);
                break;
            case 'phone':
                $repository = $doctrine->getRepository(PhoneVerify::class);
                break;
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

        // @TODO just add a getUer() method to the verifications.
        switch ($type) {
            case 'email':
                $user = $verify->getEmail()->getUser();
                break;

            case 'phone':
                $user = $verify->getPhone()->getUser();
                break;
        }


        return new PreAuthenticatedToken($user, $credentials, $providerKey, $user->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {
        $em = $this->doctrine->getManager();

        if ($this->http->checkRequestPath($request, 'user_verify_email')) {
            $repository = $this->doctrine->getRepository(EmailVerify::class);
        } elseif ($this->http->checkRequestPath($request, 'user_verify_phone')) {
            $repository = $this->doctrine->getRepository(PhoneVerify::class);
        }

        if ($verify = $request->get('token')) {
            $em->remove($verify);
            $em->flush();
        }

        // @TODO use SerializerResponseTrait
        return new Response($exception->getMessage(), 403);
    }
}
