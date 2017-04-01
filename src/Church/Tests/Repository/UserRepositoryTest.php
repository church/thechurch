<?php

namespace Church\Tests\Repository;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Repository\User\UserRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromEmail()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $class = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = new UserRepository($em, $class);

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();

        $user = $repository->createFromEmail($email);

        $this->assertInstanceOf(User::class, $user);
    }
}
