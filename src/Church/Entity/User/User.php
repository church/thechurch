<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Serializer\Annotation\Groups;

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
class User implements UserInterface, \Serializable, EquatableInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"api"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Groups({"api"})
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     * @Groups({"api"})
     */
    private $username;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"api"})
     */
    private $emails;

    /**
     * @var Email
     *
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     * @Groups({"api"})
     */
    private $primaryEmail;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Phone", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"api"})
     */
    private $phones;

    /**
     * @var Phone
     *
     * @ORM\OneToOne(targetEntity="Phone", mappedBy="phone", cascade={"all"})
     * @ORM\JoinColumn(name="primary_phone", referencedColumnName="phone")
     * @Groups({"api"})
     */
    private $primaryPhone;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="location_id")
     * @Groups({"api"})
     */
    private $location;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = 0})
     * @Groups({"api"})
     */
    private $faith;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $created;


    /**
     * Create new User.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $id = $data['id'] ?? null;
        $this->id = is_int($id) ? $id : null;

        $firstName = $data['firstName'] ?? null;
        $this->firstName = is_string($firstName) ? $firstName : null;

        $lastName = $data['lastName'] ?? null;
        $this->lastName = is_string($lastName) ? $lastName : null;

        $username = $data['username'] ?? null;
        $this->username = is_string($username) ? $username : null;

        $emails = $data['emails'] ?? [];
        if (is_array($emails)) {
            $emails = array_filter($emails, function ($email) {
                return $email instanceof Email;
            });
        }
        $this->emails = is_array($emails) ? new ArrayCollection($emails) : new ArrayCollection();

        $primaryEmail = $data['primaryEmail'] ?? null;
        $this->primaryEmail = $primaryEmail instanceof Email ? $primaryEmail : null;

        $phones = $data['phones'] ?? [];
        if (is_array($phones)) {
            $phones = array_filter($phones, function ($phone) {
                return $phone instanceof Phone;
            });
        }
        $this->phones = is_array($phones) ? new ArrayCollection($phones) : new ArrayCollection();

        $primaryPhone = $data['primaryPhone'] ?? null;
        $this->primaryPhone = $primaryPhone instanceof Phone ? $primaryPhone : null;

        $faith = $data['faith'] ?? false;
        $this->faith = is_bool($faith) ? $faith : false;

        $location = $data['location'] ?? null;
        $this->location = $location instanceof Location ? $location : null;

        $created = $data['created'] ?? null;
        $this->created = $created instanceof \DateTimeInterface ? $created : null;
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
     */
    public function getID() :? int
    {
        return $this->id;
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
    public function getPassword() : string
    {
        return '';
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
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list (
          $this->id,
          $this->username
        ) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
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
     * Set Last Name.
     *
     * @param string $lastName
     */
    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLastName() :? string
    {
        return $this->lastName;
    }


    /**
     * Add emails
     *
     * @param Email $email
     */
    public function addEmail(Email $email) : self
    {
        $this->emails[] = $email;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param Email $email
     */
    public function removeEmail(Email $email) : self
    {
        $this->emails->removeElement($email);

        return $this;
    }

    /**
     * Get emails
     *
     * @return Collection
     */
    public function getEmails() :? Collection
    {
        return $this->emails;
    }


    /**
     * Set Primary Email.
     *
     * @param Email $primaryEmail
     * @return User
     */
    public function setPrimaryEmail(Email $primaryEmail) : self
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    /**
     * Get Primary Email.
     *
     * @return Email
     */
    public function getPrimaryEmail() :? Email
    {
        return $this->primaryEmail;
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
     * @return Collection
     */
    public function getPhones() :? Collection
    {
        return $this->phones;
    }


    /**
     * Set Primary Phone.
     *
     * @param Phone $primaryPhone
     */
    public function setPrimaryPhone(Phone $primaryPhone) : self
    {
        $this->primaryPhone = $primaryPhone;

        return $this;
    }

    /**
     * Get Primary Phone.
     *
     * @return Phone
     */
    public function getPrimaryPhone() :? Phone
    {
        return $this->primaryPhone;
    }


    /**
     * Set location
     *
     * @param Location $location
     */
    public function setLocation(Location $location) : self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get current location
     */
    public function getLocation() :? Location
    {
        return $this->location;
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
     * @param \DateTime $created
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
