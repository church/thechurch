<?php

namespace Church\Entity\User\Verify;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Church\Entity\User\Email;

/**
 * Church\Entity\User\EmailVerify
 *
 * @ORM\Table(name="users_email_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class EmailVerify implements VerifyInterface
{

    /**
     * @var Email
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Email", inversedBy="verify")
     * @ORM\JoinColumn(name="email", referencedColumnName="email")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=6, unique=true)
     * @Groups({"api"})
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=6, unique=true)
     */
    private $code;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     * @Groups({"api"})
     */
    private $created;

    /**
     * Create new Email Verify.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $email = $data['email'] ?? null;
        $this->email = $email instanceof Email ? $email : null;

        $token = $data['token'] ?? null;
        $this->token = is_string($token) ? $token : null;

        $code = $data['code'] ?? null;
        $this->code = is_string($code) ? $code : null;

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
     * Set email
     *
     * @param Email $email
     * @return EmailVerify
     */
    public function setEmail(Email $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return Email
     */
    public function getEmail() :? Email
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return EmailVerify
     */
    public function setToken(string $token) : self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken() :? string
    {
        return $this->token;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return EmailVerify
     */
    public function setCode(string $code) : self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() :? string
    {
        return $this->code;
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
}