<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Serializable;

use Church\Entity\Location;
use Church\Entity\User\Email;
use Church\Entity\User\Phone;

/**
 * Church\Entity\User\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Church\Entity\User\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("primary_email")
 * @UniqueEntity("primary_phone")
 */
class User implements UserInterface, Serializable, EquatableInterface
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $emails;

    /**
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     */
    private $primary_email;

    /**
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $phones;

    /**
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="phone", cascade={"all"})
     * @ORM\JoinColumn(name="primary_phone", referencedColumnName="phone")
     */
    private $primary_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location_static", referencedColumnName="location_id")
     */
    private $location_static;

    /**
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location_current", referencedColumnName="location_id")
     */
    private $location_current;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    private $faith;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    private $isActive;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->isActive = TRUE;
        $this->email = new ArrayCollection();
        $this->phone = new ArrayCollection();
        $this->faith = FALSE;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getID()
    {
        return $this->id;
    }

    public function isActive()
    {
        return TRUE;
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
        return NULL;
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
        $roles = array(
          'ROLE_USER',
        );

        if ($this->getPassword()) {
          $roles[] = 'ROLE_PASSWORD';
        }

        if ($this->getUsername()) {
          $roles[] = 'ROLE_USERNAME';
        }

        if ($this->getFaith()) {
          $roles[] = 'ROLE_FAITH';
        }

        if ($this->getFirstName() && $this->getLastName()) {
          $roles[] = 'ROLE_NAME';
        }

        if ($email = $this->getPrimaryEmail()) {

          if ($email->getVerified()) {
            $roles[] = 'ROLE_EMAIL';
          }

        }
        else if ($phone = $this->getPrimaryPhone()) {

          if ($phone->getVerified()) {
            $roles[] = 'ROLE_PHONE';
          }

        }

        return $roles;

    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
          $this->id,
          isset($this->username) ? $this->username : NULL,
          isset($this->password) ? $this->username : NULL,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
          $this->id,
          $this->username,
          $this->password
        ) = unserialize($serialized);
    }

    /**
     * Only check for username, salt, and password, if they exist.
     *
     * @see EquatableInterface::isEqualTo()
     *
     * @link http://symfony.com/doc/current/cookbook/security/entity_provider.html
     *
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($this->getUsername()) {
          if ($this->getUsername() !== $user->getUsername()) {
            return FALSE;
          }
        }

        if ($this->getPassword()) {
          if ($this->getPassword() !== $user->getPassword()) {
            return FALSE;
          }
        }

        if ($this->getID() !== $user->getID()) {
          return FALSE;
        }

        return TRUE;
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
     * Set first_name
     *
     * @param string $first_name
     * @return User
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $last_name
     * @return User
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }


    /**
     * Add emails
     *
     * @param Email $emails
     * @return User
     */
    public function addEmail(Email $email)
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param Email $emails
     */
    public function removeEmail(Email $email)
    {
        $this->emails->removeElement($email);
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
     * @param Email $primaryEmail
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
     * @return Email
     */
    public function getPrimaryEmail()
    {
        return $this->primary_email;
    }

    /**
     * Add phones
     *
     * @param Phone $phone
     * @return User
     */
    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phones
     *
     * @param Phone $phone
     */
    public function removePhone(Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }


    /**
     * Set primary_phone
     *
     * @param Phone $primaryPhone
     * @return User
     */
    public function setPrimaryPhone(Phone $primaryPhone = null)
    {
        $this->primary_phone = $primaryPhone;

        return $this;
    }

    /**
     * Get primary_phone
     *
     * @return Phone
     */
    public function getPrimaryPhone()
    {
        return $this->primary_phone;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set static location
     *
     * @param Location $location
     * @return User
     */
    public function setLocationStatic(Location $location)
    {
        $this->location_static = $location;

        return $this;
    }

    /**
     * Get static location
     *
     * @return Location
     */
    public function getLocationStatic()
    {
        return $this->location_static;
    }


    /**
     * Set current location
     *
     * @param Location $location
     * @return User
     */
    public function setLocationCurrent(Location $location)
    {
        $this->location_current = $location;

        return $this;
    }

    /**
     * Get current location
     *
     * @return Location
     */
    public function getLocationCurrent()
    {
        return $this->location_current;
    }

    /**
     * Set faith
     *
     * @param bool $faith
     * @return User
     */
    public function setFaith($faith)
    {
        $this->faith = $faith;

        return $this;
    }

    /**
     * Get faith
     *
     * @return bool
     */
    public function getFaith()
    {
        return $this->faith;
    }

    /**
     * Set created
     *
     * @param \DateTime $verified
     * @return User
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
