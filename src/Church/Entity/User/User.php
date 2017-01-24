<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Serializable;

use Church\Entity\Location;
use Church\Entity\User\Email;
use Church\Entity\User\Phone;

/**
 * Church\Entity\User\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Church\Repository\User\UserRepository")
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
     * @Groups({"api"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     * @Groups({"api"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"api"})
     */
    private $emails;

    /**
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     * @Groups({"api"})
     */
    private $primary_email;

    /**
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"api"})
     */
    private $phones;

    /**
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="phone", cascade={"all"})
     * @ORM\JoinColumn(name="primary_phone", referencedColumnName="phone")
     * @Groups({"api"})
     */
    private $primary_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location_static", referencedColumnName="location_id")
     * @Groups({"api"})
     */
    private $location_static;

    /**
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location_current", referencedColumnName="location_id")
     * @Groups({"api"})
     */
    private $location_current;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     * @Groups({"api"})
     */
    private $faith;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $created;

    private $isActive;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->email = new ArrayCollection();
        $this->phone = new ArrayCollection();
        $this->faith = false;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue() : self
    {
        $this->created = new \DateTime();

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getID() :? int
    {
        return $this->id;
    }

    public function isActive() :? bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() :? string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() :? string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() :? string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() :? array
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
        } elseif ($phone = $this->getPrimaryPhone()) {
            if ($phone->getVerified()) {
                $roles[] = 'ROLE_PHONE';
            }
        }

        return $roles;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() : void
    {
      // Do something?
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
          $this->id,
          isset($this->username) ? $this->username : null,
          isset($this->password) ? $this->username : null,
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
    public function isEqualTo(UserInterface $user) : bool
    {
        if ($this->getUsername()) {
            if ($this->getUsername() !== $user->getUsername()) {
                return false;
            }
        }

        if ($this->getPassword()) {
            if ($this->getPassword() !== $user->getPassword()) {
                return false;
            }
        }

        if ($this->getID() !== $user->getID()) {
            return false;
        }

        return true;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername(string $username) : self
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
    public function setPassword(string $password) : self
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
    public function setFirstName(string $first_name) : self
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName() :? string
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $last_name
     * @return User
     */
    public function setLastName(string $last_name) : self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLastName() :? string
    {
        return $this->last_name;
    }


    /**
     * Add emails
     *
     * @param Email $emails
     * @return User
     */
    public function addEmail(Email $email) : self
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param Email $emails
     */
    public function removeEmail(Email $email) : self
    {
        $this->emails->removeElement($email);

        return $this;
    }

    /**
     * Get emails
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getEmails() :? Collection
    {
        return $this->emails;
    }


    /**
     * Set primary_email
     *
     * @param Email $primaryEmail
     * @return User
     */
    public function setPrimaryEmail(Email $primaryEmail) : self
    {
        $this->primary_email = $primaryEmail;

        return $this;
    }

    /**
     * Get primary_email
     *
     * @return Email
     */
    public function getPrimaryEmail() :? Email
    {
        return $this->primary_email;
    }

    /**
     * Add phones
     *
     * @param Phone $phone
     * @return User
     */
    public function addPhone(Phone $phone) : self
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phones
     *
     * @param Phone $phone
     */
    public function removePhone(Phone $phone) : self
    {
        $this->phones->removeElement($phone);

        return $this;
    }

    /**
     * Get phones
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getPhones() :? Collection
    {
        return $this->phones;
    }


    /**
     * Set primary_phone
     *
     * @param Phone $primaryPhone
     * @return User
     */
    public function setPrimaryPhone(Phone $primaryPhone) : self
    {
        $this->primary_phone = $primaryPhone;

        return $this;
    }

    /**
     * Get primary_phone
     *
     * @return Phone
     */
    public function getPrimaryPhone() :? Phone
    {
        return $this->primary_phone;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress(string $address) : self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() :? string
    {
        return $this->address;
    }

    /**
     * Set static location
     *
     * @param Location $location
     * @return User
     */
    public function setLocationStatic(Location $location) : self
    {
        $this->location_static = $location;

        return $this;
    }

    /**
     * Get static location
     *
     * @return Location
     */
    public function getLocationStatic() :? Location
    {
        return $this->location_static;
    }


    /**
     * Set current location
     *
     * @param Location $location
     * @return User
     */
    public function setLocationCurrent(Location $location) : self
    {
        $this->location_current = $location;

        return $this;
    }

    /**
     * Get current location
     *
     * @return Location
     */
    public function getLocationCurrent() :? Location
    {
        return $this->location_current;
    }

    /**
     * Set faith
     *
     * @param bool $faith
     * @return User
     */
    public function setFaith(bool $faith) : self
    {
        $this->faith = $faith;

        return $this;
    }

    /**
     * Get faith
     *
     * @return bool
     */
    public function getFaith() :? bool
    {
        return $this->faith;
    }

    /**
     * Set created
     *
     * @param \DateTime $verified
     * @return User
     */
    public function setCreated(\DateTime $created) : self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() :? \DateTime
    {
        return $this->created;
    }
}
