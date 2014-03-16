<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Church\PlaceBundle\Entity\PlaceTitle
 *
 * @ORM\Entity
 * @ORM\Table(name="place_name")
 */
class PlaceName
{
    /**
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="place_id")
     * @ORM\Id
     */
    private $place;
    
    /**
     * @ORM\Column(type="string", length=2)
     * @ORM\Id
     */
    private $language;
    
    /**
     * @ORM\Column(type="string", length=2)
     * @ORM\Id
     */
    private $country;
  
    
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
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
     * Set language
     *
     * @param string $language
     * @return PlaceTitle
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return PlaceTitle
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PlaceTitle
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
}