<?php

namespace Church\Entity\User;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Church\Entity\User\User;
use Church\Entity\User\PhoneVerify;

/**
 * Church\Entity\User\Phone
 *
 * @ORM\Table(name="users_phone")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("primary_phone")
 */
class Phone
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=35)
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="phones")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\OneToOne(targetEntity="PhoneVerify", mappedBy="phone")
     **/
    private $verify;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $verified;


    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Email
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Email
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set created
     *
     * @param \DateTime $verified
     * @return Email
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    /**
     * Set verified
     *
     * @param \DateTime $verified
     * @return Email
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;

        return $this;
    }

    /**
     * Get verified
     *
     * @return \DateTime
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * Set verify
     *
     * @param PhoneVerify $verify
     * @return Phone
     */
    public function setVerify(PhoneVerify $verify = null)
    {
        $this->verify = $verify;

        return $this;
    }

    /**
     * Get verify
     *
     * @return PhoneVerify
     */
    public function getVerify()
    {
        return $this->verify;
    }

}
