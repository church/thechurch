<?php

namespace Church\Entity\User;

use Symfony\Component\Validator\Constraints as Assert;
use Church\Validator\Constraints as ChurchAssert;

class Login
{

    /**
     * @ChurchAssert\Login
     * @Assert\Length(
     *      max = "255"
     * )
     */
    protected $value;

    public function setValue(string $value) : self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue() :? string
    {
        return $this->value;
    }

    public function __toString() : string
    {
        return $this->value;
    }
}
