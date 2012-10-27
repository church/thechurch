<?php

namespace Church\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Church\UserBundle\Entity\Email;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Church\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @UniqueEntity("username")
 * @UniqueEntity("primary_email")
 */
class User implements UserInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=88)
     */
    private $password;
    
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="email",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $emails;
    
    /**
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     */
    private $primary_email;

    
    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->emails = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add emails
     *
     * @param Church\UserBundle\Entity\Email $emails
     * @return User
     */
    public function addEmail(Email $emails)
    {
        $this->emails[] = $emails;
    
        return $this;
    }

    /**
     * Remove emails
     *
     * @param Church\UserBundle\Entity\Email $emails
     */
    public function removeEmail(Email $emails)
    {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEmails()
    {
        return $this->emails;
    }


    /**
     * Set primary_email
     *
     * @param Church\UserBundle\Entity\Email $primaryEmail
     * @return User
     */
    public function setPrimaryEmail(Email $primaryEmail = null)
    {
        $this->primary_email = $primaryEmail;
    
        return $this;
    }

    /**
     * Get primary_email
     *
     * @return Church\UserBundle\Entity\Email 
     */
    public function getPrimaryEmail()
    {
        return $this->primary_email;
    }
}