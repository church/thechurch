<?php

namespace Church\Entity\User;

use Church\Entity\Location;
use Church\Entity\Entity;
use Church\Entity\User\Email;
use Church\Entity\User\Name;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Church\Entity\User\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Church\Repository\User\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity({"primaryEmail", "username"})
 */
class User extends Entity implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * User Role.
     *
     * Granted to everyone.
     *
     * @var string.
     */
    const ROLE_ANONYMOUS = 'anonymous';

    /**
     * User Role.
     *
     * Granted to all users.
     *
     * @var string.
     */
    const ROLE_AUTHENTICATED = 'authenticated';

    /**
     * User Role.
     *
     * Granted to users with a confirmed email.
     *
     * @var string.
     */
    const ROLE_STANDARD = 'standard';

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="guid")
     * @ORM\Id
     * @Groups({"anonymous_read"})
     */
    private $id;

    /**
     * @var Name
     *
     * @ORM\Embedded(class = "Name", columnPrefix = "name_")
     * @Groups({"me_read", "neighbor_read"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, unique=true, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 15
     * )
     * @Assert\Regex(
     *     pattern="/^[a-z\d][a-z\d_]*[a-z\d]$/",
     *     match=true,
     *     message="Username must consist of alphanumeric characters and underscores"
     * )
     * @Groups({"anonymous_read", "me_write"})
     */
    private $username;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * @Groups({"me_read"})
     */
    private $emails;

    /**
     * @var Email
     *
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     * @Groups({"me_read"})
     */
    private $primaryEmail;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Church\Entity\Location")
     * @ORM\JoinColumn(name="location", referencedColumnName="location_id")
     * @Groups({"me_read", "neighbor_read"})
     */
    private $location;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = 0})
     * @Groups({"me_read", "me_write"})
     */
    private $orthodox;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = 1})
     * @Groups({"me_read", "me_write"})
     */
    private $enabled;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"anonymous_read"})
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
        $name = $this->getSingle($name, Name::class);
        $this->name = $name ? $name : new Name();

        $username = $data['username'] ?? null;
        $this->username = is_string($username) ? $username : null;

        $emails = $data['emails'] ?? [];
        $this->emails = $this->getMultiple($emails, Email::class);

        $primaryEmail = $data['primaryEmail'] ?? null;
        $this->primaryEmail = $this->getSingle($primaryEmail, Email::class);

        $orthodox = $data['orthodox'] ?? false;
        $this->orthodox = is_bool($orthodox) ? $orthodox : false;

        $enabled = $data['enabled'] ?? true;
        $this->enabled = is_bool($enabled) ? $enabled : true;

        $location = $data['location'] ?? null;
        $this->location = $this->getSingle($location, Location::class);

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
     * Set Username.
     *
     * @param string $username
     */
    public function setUsername(string $username) : self
    {
        $this->username = $username;

        return $this;
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
        return null;
    }

    /**
     * @inheritDoc
     *
     * @Groups({"me_read"})
     */
    public function getRoles() : array
    {
        $roles = [
            self::ROLE_ANONYMOUS,
            self::ROLE_AUTHENTICATED,
        ];

        if ($this->getPrimaryEmail()
            && $this->getPrimaryEmail()->getVerified()
            && $this->getName()->getFirst()
            && $this->getName()->getLast()
            && $this->isOrthodox()
            && $this->getUsername()
            && $this->getLocation()
        ) {
            $roles[] = self::ROLE_STANDARD;
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
            $this->id
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list (
          $this->id
        ) = unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user) : bool
    {
        if ($this->getId() !== $user->getId()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the current user and the requested user are in the same
     * place.
     *
     * @param User $user
     */
    public function isNeighbor(User $user) : bool
    {
        if (!$this->location) {
            return false;
        }

        if (!$this->location->getPlace()) {
            return false;
        }

        if (!$user->getLocation()) {
            return false;
        }

        if (!$user->getLocation()->getPlace()) {
            return false;
        }

        if (!in_array(self::ROLE_STANDARD, $this->getRoles())) {
            return false;
        }

        if (!in_array(self::ROLE_STANDARD, $user->getRoles())) {
            return false;
        }

        return $this->location->getPlace()->getId() === $user->getLocation()->getPlace()->getId();
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
     * Set Orthodox
     *
     * @param bool $orthodox
     */
    public function setOrthodox(bool $orthodox) : self
    {
        $this->orthodox = $orthodox;

        return $this;
    }

    /**
     * Get Orthodox
     *
     * @return bool
     */
    public function isOrthodox() : bool
    {
        return $this->orthodox;
    }

    /**
     * Set Enabled
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled) : self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get Enabled.
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->enabled;
    }

    /**
     * Set created
     *
     * @param \DateTimeInterface $created
     */
    public function setCreated(\DateTimeInterface $created) : self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() :? \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * Get Color.
     *
     * @Groups({"anonymous_read"})
     */
    public function getColor() :? string
    {
        return $this->username ? '#' . substr(md5($this->username), 0, 6) : null;
    }
}
