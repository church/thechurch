<?php

namespace Church\Tests\Entity\User;

use Church\Entity\User\User;
use Church\Tests\Entity\EntityTest;

class UserTest extends EntityTest
{

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
}
