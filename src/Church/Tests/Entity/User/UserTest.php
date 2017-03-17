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
    }
}
