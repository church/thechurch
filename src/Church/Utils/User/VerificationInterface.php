<?php

namespace Church\Utils\User;

interface VerificationInterface
{

    /**
     * Creates a new verification.
     */
    public function create(string $item);

    /**
     * Sends a verification by the appropriate method.
     */
    public function send($item);
}
