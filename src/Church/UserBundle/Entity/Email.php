<?php

namespace Church\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Church\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Church\UserBundle\Entity\Email
 *
 * @ORM\Table(name="users_email")
 * @ORM\Entity
 * @UniqueEntity("primary_email")
 */
class Email
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="emails")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="string", unique=true, length=255)
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $verified;
    
    public function __construct()
    {
        $this->verified = FALSE;
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
     * Set user
     *
     * @param Church\UserBundle\Entity\User $user
     * @return Email
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return Church\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set verified
     *
     * @param boolean $verified
     * @return Email
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    
        return $this;
    }

    /**
     * Get verified
     *
     * @return boolean 
     */
    public function getVerified()
    {
        return $this->verified;
    }
}