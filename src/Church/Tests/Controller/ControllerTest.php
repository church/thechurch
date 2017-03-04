<?php

namespace Church\Tests\Controller;

use Church\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class ControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    const FORMAT = 'json';

    /**
     * Gets the mock serializer.
     */
    protected function getSerializer()
    {
        return $this->createMock(SerializerInterface::class);
    }

    /**
     * Gets the mock doctrine.
     */
    protected function getDoctrine()
    {
        return $this->createMock(RegistryInterface::class);
    }

    /**
     * Gets the mock token storage.
     */
    protected function getTokenStorage()
    {
        return $this->createMock(TokenStorageInterface::class);
    }
}
