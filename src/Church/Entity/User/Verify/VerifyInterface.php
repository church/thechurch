<?php

namespace Church\Entity\User\Verify;

use Church\Entity\EntityInterface;

interface VerifyInterface extends EntityInterface
{
    /**
     * Get Created Date.
     */
    public function getCreated() :? \DateTimeInterface;
}
