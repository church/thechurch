<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Church\Entity\User\User;
use Church\Entity\User\Email;


/**
 * Church\Entity\User\EmailVerify
 *
 * @ORM\Table(name="users_email_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class EmailVerify
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Email", inversedBy="verify")
     * @ORM\JoinColumn(name="email", referencedColumnName="email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Set email
     *
     * @param Email $email
     * @return EmailVerify
     */
    public function setEmail(Email $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return EmailVerify
     */
    public function setToken($token)
    {

        // Ensure that token is at least 6 characters.
        $this->token = str_pad($token, 6, '0', STR_PAD_LEFT);

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set created
     *
     * @param \DateTime $verified
     * @return Email
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

}
