<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Church\PlaceBundle\Entity\City
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="city")
 * @UniqueEntity("slug")
 */
class City
{
    /**
     * @ORM\OneToOne(targetEntity="Place", inversedBy="city")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="place_id")
     * @ORM\Id
     */
    private $place;
    
    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $slug;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $created;
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }
    
    /**
     * Set place
     *
     * @param \Church\PlaceBundle\Entity\Place $id
     * @return PlaceTitle
     */
    public function setPlace(\Church\PlaceBundle\Entity\Place $place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return \Church\PlaceBundle\Entity\Place 
     */
    public function getPlace()
    {
        return $this->place;
    }
  

    /**
     * Set slug
     *
     * @param string $slug
     * @return PlaceTitle
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
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