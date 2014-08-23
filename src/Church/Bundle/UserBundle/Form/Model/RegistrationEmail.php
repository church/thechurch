<?php

namespace Church\Bundle\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class RegistrationEmail
{

    /**
     * @Assert\Email()
     * @Assert\Length(
     *      max = "255"
     * )
     */
    protected $email;

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

}
