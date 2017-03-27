<?php

namespace Church\Tests\Controller;

use Church\Serializer\SerializerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
     * Gets the mock entity manager.
     */
    protected function getEntityManager()
    {
        return $this->createMock(EntityManagerInterface::class);
    }

    /**
     * Gets the mock entity manager.
     */
    protected function getRepository()
    {
        return $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
