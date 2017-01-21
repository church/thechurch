<?php

namespace Church\Controller;

use Church\Response\SerializerResponseTrait;
use Church\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{

    use SerializerResponseTrait;

    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    public function __construct(
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Determine if current user is logged in.
     */
    protected function isLoggedIn() : bool
    {
        try {
            $this->getUser();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() : User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new \Exception('Not Logged In.');
        }

        if (!is_object($user = $token->getUser())) {
            throw new \Exception('Not Logged In.');
        }

        return $user;
    }
}
