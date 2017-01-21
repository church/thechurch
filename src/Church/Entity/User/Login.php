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
    protected $username;

    public function setUsername(string $username) : self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername() :? string
    {
        return $this->username;
    }
}
