<?php

namespace Church\Security;

use Church\Controller\Controller;
use Church\Entity\User\User;
use Church\Entity\User\Verify\EmailVerify;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Code Authenticator.
 */

class CodeAuthenticator extends Controller implements
    SimplePreAuthenticatorInterface,
    AuthenticationFailureHandlerInterface
{
    /**
     * @var HttpUtils
     */
    protected $http;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage,
        HttpUtils $http
    ) {
        parent::__construct($serializer, $validator, $doctrine, $tokenStorage);
        $this->http = $http;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        // Only Create a Token when on a verify path and get the type.
        if (!$this->http->checkRequestPath($request, 'me_verify_email')) {
            return;
        }

        $input = $this->deserialize($request, EmailVerify::class);

        $credentials = [
            'token' => $input->getToken(),
            'code' => $input->getCode(),
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

        // User is already in the session.
        $user = $token->getUser();
        if ($user instanceof User) {
            return new PreAuthenticatedToken($user, $credentials, $providerKey, $user->getRoles());
        }

        $repository = $this->doctrine->getRepository(EmailVerify::class);

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


        $user = $verify->getEmail()->getUser();
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

        throw new AccessDeniedException($exception->getMessage(), null, $exception);
    }
}
