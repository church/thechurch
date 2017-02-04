<?php

namespace Church\Entity\User;

use Church\Entity\EntityInterface;
use Church\Entity\User\User;
use Church\Entity\User\PhoneVerify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Church\Entity\User\Phone
 *
 * @ORM\Table(name="users_phone")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("primary_phone")
 */
class Phone implements EntityInterface
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=35)
     * @Groups({"api"})
     */
    private $phone;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="phones")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $created;

    /**
     * @var PhoneVerify
     *
     * @ORM\OneToOne(targetEntity="PhoneVerify", mappedBy="phone")
     * @Groups({"api"})
     **/
    private $verify;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"api"})
     */
    private $verified;

    /**
     * Creates a new Phone.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $phone = $data['phone'] ?? null;
        $this->phone = is_string($phone) ? $phone : null;

        $user = $data['user'] ?? null;
        $this->user = $user instanceof User ? $user : null;

        $created = $data['created'] ?? null;
        $this->created = $created instanceof \DateTimeInterface ? $created : null;

        $verify = $data['verify'] ?? null;
        $this->verify = $verify instanceof PhoneVerify ? $verify : null;

        $verified = $data['verified'] ?? null;
        $this->verified = $verified instanceof \DateTimeInterface ? $verified : null;
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
     * Set phone
     *
     * @param string $phone
     * @return Email
     */
    public function setPhone(string $phone) : self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() :? string
    {
        return $this->phone;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Email
     */
    public function setUser(User $user) : self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser() :? User
    {
        return $this->user;
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


    /**
     * Set verified
     *
     * @param \DateTime $verified
     * @return Email
     */
    public function setVerified(\DateTime $verified) : self
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return \DateTime
     */
    public function getVerified() :? \DateTime
    {
        return $this->verified;
    }

    /**
     * Set verify
     *
     * @param PhoneVerify $verify
     * @return Phone
     */
    public function setVerify(PhoneVerify $verify) : self
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Get verify
     *
     * @return PhoneVerify
     */
    public function getVerify() :? PhoneVerify
    {
        return $this->verify;
    }
}
