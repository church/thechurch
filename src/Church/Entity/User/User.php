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
 * @UniqueEntity("primary_email")
 */
class User implements EntityInterface, UserInterface, \Serializable, EquatableInterface
{

    /**
     * User Role.
     *
     * Granted to everyone.
     *
     * @var string.
     */
    const ROLE_PUBLIC = 'public';

    /**
     * User Role.
     *
     * Granted to all users.
     *
     * @var string.
     */
    const ROLE_USER = 'user';

    /**
     * User Role.
     *
     * Granted to users with a confirmed email.
     *
     * @var string.
     */
    const ROLE_EMAIL = 'email';

    /**
     * User Role.
     *
     * Granted to users with a name
     *
     * @var string.
     */
    const ROLE_NAME = 'name';

    /**
     * User Role.
     *
     * Granted to who have agreed to the statement of faith.
     *
     * @var string.
     */
    const ROLE_FAITH = 'faith';

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="guid")
     * @ORM\Id
     * @Groups({"public", "me"})
     */
    private $id;

    /**
     * @var Name
     *
     * @ORM\Embedded(class = "Name", columnPrefix = "name_")
     * @Groups({"public", "me", "email"})
     */
    private $name;

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
     * @Groups({"public", "me"})
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
        return null;
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
     * @Groups({"me"})
     */
    public function getRoles() : array
    {
        $roles = [
            self::ROLE_PUBLIC,
            self::ROLE_USER,
        ];

        if ($email = $this->getPrimaryEmail()) {
            if ($email->getVerified()) {
                $roles[] = self::ROLE_EMAIL;
            }
        }

        if ($this->getName()->getFirst() && $this->getName()->getLastName()) {
            $roles[] = self::ROLE_NAME;
        }

        if ($this->getFaith()) {
            $roles[] = self::ROLE_FAITH;
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
}
