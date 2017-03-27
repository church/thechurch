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
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * Creates the Controller.
     *
     * @param SerializerInterface $serializer
     * @param RegistryInterface $doctrine
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine
    ) {
        $this->serializer = $serializer;
        $this->doctrine = $doctrine;
    }
}
