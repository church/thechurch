<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use Church\Entity\User\User;
use Church\Entity\User\EmailVerify;

/**
 * Church\Entity\User\Email
 *
 * @ORM\Table(name="users_email")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("primary_email")
 */
class Email
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * @Groups({"api"})
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
     * @Groups({"api"})
     */
    private $created;

    /**
     * @var EmailVerify
     *
     * @ORM\OneToOne(targetEntity="EmailVerify", mappedBy="email")
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
     * Create new Email.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $email = $data['email'] ?? null;
        $this->email = is_string($data['email']) ? $data['email'] : null;

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
     * @param \DateTime $verified
     * @return Email
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
     * @param EmailVerify $verify
     * @return Email
     */
    public function setVerify(EmailVerify $verify) : self
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Get verify
     *
     * @return EmailVerify
     */
    public function getVerify() :? EmailVerify
    {
        return $this->verify;
    }
}
