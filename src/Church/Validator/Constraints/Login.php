<?php

namespace Church\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Login extends Constraint
{
    public $message = 'Not a valid email address or phone number.';

    public function validatedBy()
    {
        return 'valid_login';
    }
}
