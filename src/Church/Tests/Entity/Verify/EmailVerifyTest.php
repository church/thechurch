<?php

namespace Church\Tests\Entity\Verify;

use Church\Entity\User\Verify\EmailVerify;
use Church\Entity\User\Verify\VerifyInterface;
use Church\Tests\Entity\EntityTest;

class EmailVerifyTest extends EntityTest
{

    public function testIsEqualTo()
    {
        $other = $this->createMock(VerifyInterface::class);

        $email = new EmailVerify();

        $this->assertFalse($email->isEqualTo($other));
    }
}
