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

  /**
   * @var \Egulias\EmailValidator\EmailValidator
   */
    protected $email;

    /**
     * @var \libphonenumber\PhoneNumberUtil
     */
    protected $phone;

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
        return $this->email->isValid($value);
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
            $number = $this->phone->parse($value, 'US');
        } catch (NumberParseException $e) {
            return false;
        }

        return $this->email->isValidNumber($number);
    }
}
