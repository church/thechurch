<?php

namespace Church\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Egulias\EmailValidator\EmailValidator;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;

/**
 * @Annotation
 */
class LoginValidator extends ConstraintValidator
{

    private $email;

    private $phone;

    public function __construct(EmailValidator $email, PhoneNumberUtil $phone)
    {
        $this->email = $email;
        $this->phone = $phone;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$this->isEmail($value)) {
            if (!$this->isPhone($value)) {
                return $this->context->buildViolation($constraint->message)
                                     ->addViolation();

            }
        }
    }

    /**
     * Determine if value is an email.
     *
     * @param string $value String to test if is an email.
     *
     * @return bool
     */
    public function isEmail($value)
    {
        return $this->getEmail()->isValid($value);
    }

    /**
     * Determine if value is phone number.
     *
     * @param string $value String to test if is an phone.
     *
     * @return bool
     */
    public function isPhone($value)
    {
        try {
            $number = $this->getPhone()->parse($value, 'US');
        } catch (NumberParseException $e) {
            return false;
        }

        return $this->getPhone()->isValidNumber($number);
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }
}
