<?php

namespace Church\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Login
{

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $_username;

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $_password;

    public function setUsername($username)
    {
        $this->_username = $username;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
    }

    public function getPassword()
    {
        return $this->_password;
    }

}
