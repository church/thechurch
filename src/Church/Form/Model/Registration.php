<?php

namespace Church\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Registration
{

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $name;

    /**
     * @Assert\Email()
     * @Assert\MaxLength(255)
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $address;


    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

}
