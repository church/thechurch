<?php

namespace Church\Security;

use Church\Entity\User\User;
use Church\Entity\User\Verify\EmailVerify;
use Church\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Code Authenticator.
 */
class CodeAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var HttpUtils
     */
    protected $http;

    /**
     * Creates the Code Authenticator
     *
     * @param SerializerInterface $serializer
     * @param RegistryInterface $doctrine
     * @param HttpUtils $http
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine,
        HttpUtils $http
    ) {
        $this->serializer = $serializer;
        $this->doctrine = $doctrine;
        $this->http = $http;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        // Only Create a Token when on a verify path and get the type.
        if (!$this->http->checkRequestPath($request, 'me_login_email')) {
            return;
        }

        $credentials = $this->serializer->deserialize($request, EmailVerify::class);

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

        if ($verify = $repository->findOneByToken($credentials->getToken())) {
            $created = clone $verify->getCreated();
            $created->add(new \DateInterval('PT1H'));

            $now = new \DateTime('now');

            if ($created < $now) {
                throw new AuthenticationException('Verification Code is older than 1 hour');
            }

            if ($verify->isEqualTo($credentials)) {
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
