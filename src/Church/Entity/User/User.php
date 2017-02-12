<?php

namespace Church\Entity\User;

use Church\Entity\Location;
use Church\Entity\User\Email;
use Church\Entity\User\Name;
use Church\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Church\Entity\User\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Church\Repository\User\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("primary_email")
 */
class User implements EntityInterface, UserInterface, \Serializable, EquatableInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="guid")
     * @ORM\Id
     * @Groups({"me", "api"})
     */
    private $id;

    /**
     * @var Name
     *
     * @ORM\Embedded(class = "Name", columnPrefix = "name_")
     * @Groups({"me", "api"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     * @Groups({"me", "api"})
     */
    private $username;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"me"})
     */
    private $emails;

    /**
     * @var Email
     *
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     * @Groups({"me"})
     */
    private $primaryEmail;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="location_id")
     * @Groups({"me"})
     */
    private $location;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = 0})
     * @Groups({"me"})
     */
    private $faith;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"me", "api"})
     */
    private $created;

    /**
     * Create new User.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $id = $data['id'] ?? null;
        $this->id = is_string($id) && uuid_is_valid($id) ? strtolower($id) : strtolower(uuid_create(UUID_TYPE_DEFAULT));

        $name = $data['name'] ?? null;
        if ($name instanceof Name) {
            $this->name = $name;
        } elseif (is_array($name)) {
            $this->name = new Name($name);
        } else {
            $this->name = new Name();
        }

        $lastName = $data['lastName'] ?? null;
        $this->lastName = is_string($lastName) ? $lastName : null;

        $username = $data['username'] ?? null;
        $this->username = is_string($username) ? $username : null;

        $emails = $data['emails'] ?? [];
        if (is_array($emails)) {
            $emails = array_map(function ($item) {
                if (!is_array($item)) {
                    return $item;
                }

                return new Email($item);
            }, $emails);
            $emails = array_filter($emails, function ($email) {
                return $email instanceof Email;
            });
        }
        $this->emails = is_array($emails) ? new ArrayCollection($emails) : new ArrayCollection();

        $primaryEmail = $data['primaryEmail'] ?? null;
        $this->primaryEmail = $primaryEmail instanceof Email ? $primaryEmail : null;

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
    public function getId() :? string
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

        if ($this->getName()->getFirst() && $this->getName()->getLastName()) {
            $roles[] = 'ROLE_NAME';
        }

        if ($email = $this->getPrimaryEmail()) {
            if ($email->getVerified()) {
                $roles[] = 'ROLE_EMAIL';
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
     * Set Name.
     *
     * @param Name $name
     */
    public function setName(Name $name) : self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Name.
     */
    public function getName() :? Name
    {
        return $this->name;
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
