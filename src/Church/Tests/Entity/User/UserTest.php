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
}
