<?php

namespace Church\Controller;

use Church\Entity\User\User;
use Church\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * An abstract controller to extend.
 */
abstract class Controller
{

    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Creates the Controller.
     *
     * @param SerializerInterface $serializer
     * @param RegistryInterface $doctrine
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage
    ) {
        $this->serializer = $serializer;
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() :? User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!is_object($user)) {
            return null;
        }

        return $user;
    }
}
