<?php

namespace Church\Entity\User\Verify;

use Church\Entity\User\Phone;
use Doctrine\ORM\Mapping as ORM;

/**
 * Church\Entity\User\PhoneVerify
 *
 * @ORM\Table(name="users_phone_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class PhoneVerify implements VerifyInterface
{

    /**
     * @var Phone
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Phone", inversedBy="verify")
     * @ORM\JoinColumn(name="phone", referencedColumnName="phone")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=6, unique=true)
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
     */
    private $created;

    /**
     * Creates a new Phone Verification.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $phone = $data['phone'] ?? null;
        $this->phone = $phone instanceof Phone ? $phone : null;

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
     * Set phone
     *
     * @param Phone $phone
     * @return PhoneVerify
     */
    public function setPhone(Phone $phone) : self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return Phone
     */
    public function getPhone() :? Phone
    {
        return $this->phone;
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
     * Get code
     *
     * @return string
     */
    public function getCode() :? string
    {
        return $this->code;
    }

    /**
     * Set token
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
     * Get token
     *
     * @return string
     */
    public function getToken() :? string
    {
        return $this->token;
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