<?php

namespace Church\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Validator\Constraints as Assert;

use Church\UserBundle\Entity\User;


/**
 * Church\UserBundle\Entity\EmailVerify
 *
 * @ORM\Table(name="users_email_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("primary_email")
 */
class EmailVerify
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="email", referencedColumnName="email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $verification;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * Construct
     */
    public function __construct()
    {
        $generator = new SecureRandom();
        $token = mb_convert_encoding($generator->nextBytes(16), 'UTF-8');
        $this->setVerification($token);
    }

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
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set verification
     *
     * @param string $verification
     * @return EmailVerify
     */
    public function setVerification($verification)
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * Get verification
     *
     * @return string
     */
    public function getVerification()
    {
        return $this->verification;
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
