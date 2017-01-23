<?php

namespace Church\Entity\User;

use Symfony\Component\Validator\Constraints as Assert;
use Church\Validator\Constraints as ChurchAssert;

class Login
{

    protected const TYPES = [
      'phone',
      'email',
    ];

    /**
     * @ChurchAssert\Login
     * @Assert\Length(
     *      max = "255"
     * )
     */
    protected $value;

    /**
     * @Assert\Choice(callback = "getTypes")
     */
    protected $type;

    public function setValue(string $value) : self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue() :? string
    {
        return $this->value;
    }

    public function setType(string $type) : self
    {
        if (!in_array($type, self::TYPES)) {
            throw new \LogicException('Type must be valid.');
        }

        $this->type = $type;

        return $this;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getTypes() : array
    {
        return self::TYPES;
    }

    public function isEmail() : bool
    {
        return $this->type === 'email';
    }

    public function isPhone() : bool
    {
        return $this->type === 'phone';
    }

    public function __toString() : string
    {
        return $this->value;
    }
}
