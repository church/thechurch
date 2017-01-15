<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\Validator\Constraints as Assert;

use Church\Entity\User\User;
use Church\Entity\User\Phone;

/**
 * Church\Entity\User\PhoneVerify
 *
 * @ORM\Table(name="users_phone_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class PhoneVerify
{

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Phone", inversedBy="verify")
     * @ORM\JoinColumn(name="phone", referencedColumnName="phone")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=6, unique=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=6, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

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
