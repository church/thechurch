<?php

namespace Church\Form\Model;

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

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }
}
