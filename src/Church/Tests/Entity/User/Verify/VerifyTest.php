<?php

namespace Church\Tests\Entity\User\Verify;

use Church\Tests\Entity\EntityTest;

abstract class VerifyTest extends EntityTest
{

    public function testIsEqualTo()
    {
        $data = [
            'token' => 'abc',
            'code' => '123',
        ];

        $verify = new $this->class($data);

        $this->assertFalse($verify->isEqualTo($verify));

        $verify->hashData();

        $this->assertTrue($verify->isEqualTo($verify));
    }
}
