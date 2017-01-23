<?php

namespace Church\Validator\Constraints;

interface LoginValidatorInterface
{
    /**
     * Determine if value is an email.
     *
     * @param string $value String to test if is an email.
     *
     * @return bool
     */
    public function isEmail(string $value) : bool;

    /**
     * Determine if value is phone number.
     *
     * @param string $value String to test if is an phone.
     *
     * @return bool
     */
    public function isPhone(string $value) : bool;
}
