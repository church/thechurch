<?php

namespace Church\Utils\User;

use Church\Entity\User\VerifyInterface;

interface VerificationInterface
{

    /**
     * Creates a new verification.
     */
    public function create(string $item) : VerifyInterface;

    /**
     * Sends a verification by the appropriate method.
     */
    public function send(VerifyInterface $item) : bool;
}
