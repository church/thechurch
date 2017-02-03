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
class LoginValidator extends ConstraintValidator implements LoginValidatorInterface
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

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function isEmail(string $value) : bool
    {
        return $this->email->isValid($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isPhone(string $value) : bool
    {
        try {
            $number = $this->phone->parse($value, 'US');
        } catch (NumberParseException $e) {
            return false;
        }

        return $this->phone->isValidNumber($number);
    }
}
