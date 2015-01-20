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

    public function __construct(EmailValidator $email, PhoneNumberUtil $phone) {
      $this->email = $email;
      $this->phone = $phone;
    }

    public function validate($value, Constraint $constraint)
    {

        if (!$this->getEmail()->isValid($value)) {

          try {
            $number = $this->getPhone()->parse($value, 'US');
          }
          catch (NumberParseException $e) {
            return $this->context->buildViolation($constraint->message)
                                 ->addViolation();
          }

          if (!$this->getPhone()->isValidNumber($number)) {

            return $this->context->buildViolation($constraint->message)
                                 ->addViolation();

          }

        }

    }

    public function setEmail($email) {
      $this->email = $email;
      return $this;
    }

    public function getEmail() {
      return $this->email;
    }

    public function setPhone($phone) {
      $this->phone = $phone;
      return $this;
    }

    public function getPhone() {
      return $this->phone;
    }

}
