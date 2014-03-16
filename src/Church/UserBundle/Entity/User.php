<?php

namespace Church\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Church\UserBundle\Entity\Email;
use Church\PlaceBundle\Entity\Place;

/**
 * Church\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Church\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("username")
 * @UniqueEntity("primary_email")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=25, unique=true, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=88, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $salt;

    /**
     * @ORM\OneToMany(targetEntity="Email", mappedBy="user",  cascade={"all"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity="Email", mappedBy="email", cascade={"all"})
     * @ORM\JoinColumn(name="primary_email", referencedColumnName="email")
     */
    private $primary_email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="Church\PlaceBundle\Entity\Place")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="place_id")
     */
    private $place;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    private $isActive;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->isActive = TRUE;
        $this->salt = md5(uniqid(null, true));
        $this->email = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getID()
    {
        return $this->id;
    }

    public function isActive()
    {
        return TRUE;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
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
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set first_name
     *
     * @param string $first_name
     * @return User
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $last_name
     * @return User
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }


    /**
     * Add emails
     *
     * @param Church\UserBundle\Entity\Email $emails
     * @return User
     */
    public function addEmail(Email $email)
    {
        $this->emails[] = $emails;

        return $this;
    }

    /**
     * Remove emails
     *
     * @param Church\UserBundle\Entity\Email $emails
     */
    public function removeEmail(Email $email)
    {
        $this->email->removeElement($email);
    }

    /**
     * Get emails
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getEmails()
    {
        return $this->email;
    }


    /**
     * Set primary_email
     *
     * @param Church\UserBundle\Entity\Email $primaryEmail
     * @return User
     */
    public function setPrimaryEmail(Email $primaryEmail = null)
    {
        $this->primary_email = $primaryEmail;

        return $this;
    }

    /**
     * Get primary_email
     *
     * @return Church\UserBundle\Entity\Email
     */
    public function getPrimaryEmail()
    {
        return $this->primary_email;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }


    /**
     * Set place
     *
     * @param Church\PlaceBundle\Entity\Place $place
     * @return User
     */
    public function setPlace(\Church\PlaceBundle\Entity\Place $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return Church\PlaceBundle\Entity\Place
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return User
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return User
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
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
}
