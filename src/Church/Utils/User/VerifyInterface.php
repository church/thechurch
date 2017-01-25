<?php

namespace Church\Utils\User;

interface VerifyInterface
{

    /**
     * Creates a new verification.
     */
    public function create(string $item);
}
