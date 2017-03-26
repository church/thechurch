<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use Church\Entity\User\User;
use Church\Entity\User\Verify\EmailVerify;

/**
 * Church\Entity\User\Email
 *
 * @ORM\Table(name="users_email")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Email
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     strict = true,
     *     checkMX = true
     * )
     * @Groups({"me_read", "me_write"})
     */
    private $email;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="emails")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"me_read"})
     */
    private $created;

    /**
     * @var EmailVerify
     *
     * @ORM\OneToOne(
     *  targetEntity="\Church\Entity\User\Verify\EmailVerify",
     *  mappedBy="email",
     *  cascade={"remove"}
     * )
     */
    private $verify;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"me_read"})
     */
    private $verified;

    /**
     * Create new Email.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $email = $data['email'] ?? null;
        $this->email = is_string($email) ? $email : null;

        $user = $data['user'] ?? null;
        $this->user = $user instanceof User ? $user : null;

        $created = $data['created'] ?? null;
        $this->created = $created instanceof \DateTimeInterface ? $created : null;

        $verify = $data['verify'] ?? null;
        $this->verify = $verify instanceof EmailVerify ? $verify : null;

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
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() :? string
    {
        return $this->email;
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
     * Get verified.
     */
    public function getVerified() :? \DateTimeInterface
    {
        return $this->verified;
    }

    /**
     * Set verify
     *
     * @param EmailVerify $verify
     */
    public function setVerify(EmailVerify $verify) : self
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Get verify
     */
    public function getVerify() :? EmailVerify
    {
        return $this->verify;
    }

    /**
     * Convers the email object to a string.
     */
    public function __toString() : string
    {
        return $this->email;
    }

    /**
     * Determines if one email is equal to another.
     */
    public function isEqualTo(Email $email) : bool
    {
        if ($this->email !== $email->getEmail()) {
            return false;
        }

        if (!$this->user || !$email->getUser()) {
            return false;
        }

        if (!$this->user->isEqualTo($email->getUser())) {
            return false;
        }

        return true;
    }
}
