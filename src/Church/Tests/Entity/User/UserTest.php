<?php

namespace Church\Tests\Entity\User;

use DateTimeInterface;
use Church\Entity\User\User;
use Church\Tests\Entity\EntityTest;

class UserTest extends EntityTest
{

    public function testSetCreatedValue()
    {
        $user = new User();

        $user->setCreatedValue();

        $this->assertInstanceOf(DateTimeInterface::class, $user->getCreated());
    }

    public function testSetNameUser()
    {
        $user = new User();

        $user->setNameUser();

        $this->assertSame($user, $user->getName()->getUser());
    }

    public function testGetId()
    {
        $id = '897cc71a-e9c4-4f3f-952f-be20bd2a2018';
        $user = new User([
            'id' => $id,
        ]);

        $this->assertEquals($id, $user->getId());
    }

    public function testSetUsername()
    {
        $user = new User();

        $username = 'test';
        $user->setUsername($username);

        $this->assertEquals($username, $user->getUsername());
    }

    public function testGetSalt()
    {
        $user = new User();

        $this->assertNull($user->getSalt());
    }

    public function testGetPassword()
    {
        $user = new User();

        $this->assertNull($user->getPassword());
    }

    public function testGetRoles()
    {
        $user = new User();

        $roles = $user->getRoles();
        $this->assertEquals([
            'anonymous',
            'authenticated',
        ], $roles);

        $user = new User([
            'name' => [
                'first' => 'Test',
                'last' => 'User',
            ],
            'primaryEmail' => [
                'email' => 'test@example.com',
                'verified' => new \DateTime()
            ],
            'orthodox' => true,
            'username' => 'test',
            'location' => [
                'id' => 1234,
            ],
        ]);

        $roles = $user->getRoles();
        $this->assertEquals([
            'anonymous',
            'authenticated',
            'standard',
        ], $roles);
    }

    public function testIsNeighbor()
    {
        $user = new User();
        $other = new User();

        $this->assertFalse($user->isNeighbor($other));

        $user = new User([
            'name' => [
                'first' => 'Test',
                'last' => 'User',
            ],
            'primaryEmail' => [
                'email' => 'test@example.com',
                'verified' => new \DateTime()
            ],
            'orthodox' => true,
            'username' => 'test',
            'location' => [
                'id' => '123',
                'place' => [
                    'id' => 874397665,
                ],
            ],
        ]);

        $other = $user = new User([
            'name' => [
                'first' => 'Other',
                'last' => 'User',
            ],
            'primaryEmail' => [
                'email' => 'other@example.com',
                'verified' => new \DateTime()
            ],
            'orthodox' => true,
            'username' => 'test',
            'location' => [
                'id' => '456',
                'place' => [
                    'id' => 874397665,
                ],
            ],
        ]);

        $this->assertTrue($user->isNeighbor($other));
    }

    public function testEraseCredentials()
    {
        $user = new User();

        $this->assertNull($user->eraseCredentials());
    }

    public function testSerialize()
    {
        $id = '897cc71a-e9c4-4f3f-952f-be20bd2a2018';
        $user = new User([
            'id' => $id,
        ]);

        $this->assertEquals(serialize([$id]), $user->serialize());
    }

    public function testUnserialize()
    {
        $id = '897cc71a-e9c4-4f3f-952f-be20bd2a2018';
        $user = new User();

        $user->unserialize(serialize([$id]));

        $this->assertEquals($id, $user->getId());
    }

    public function testIsEqualTo()
    {
        $id = '897cc71a-e9c4-4f3f-952f-be20bd2a2018';
        $user = new User([
            'id' => $id,
        ]);

        $this->assertTrue($user->isEqualTo($user));
    }
}
