<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Church\PlaceBundle\Entity\Place
 *
 * @ORM\Entity
 * @ORM\Table(name="place")
 */
class Place
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="place_id", type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="parent", referencedColumnName="place_id")
     */
    private $parent;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $type;
    
    /**
     * @ORM\OneToMany(targetEntity="PlaceName", mappedBy="place", cascade={"all"})
     */
    private $name;
    
    /**
     * @ORM\OneToOne(targetEntity="City", mappedBy="place")
     */
    private $city;
    
    /**
     * @ORM\Column(type="decimal", precision=8, scale=6)
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $longitude;
    
    
    /**
     * Construct
     */
    public function __construct()
    {
        $this->name = new ArrayCollection();
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

    /**
     * Set id
     *
     * @param integer $id
     * @return Place
     */
    public function setID($id)
    {
        $this->id = $id;
    
        return $this;
    }
    
    /**
     * Add name
     *
     * @param \Church\PlaceBundle\Entity\PlaceName $name
     * @return Place
     */
    public function addName(\Church\PlaceBundle\Entity\PlaceName $name)
    {
        $this->name[] = $name;
    
        return $this;
    }

    /**
     * Remove name
     *
     * @param \Church\PlaceBundle\Entity\PlaceName $name
     */
    public function removeName(\Church\PlaceBundle\Entity\PlaceName $name)
    {
        $this->name->removeElement($name);
    }

    /**
     * Get name
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set type
     *
     * @param integer $type
     * @return Place
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
    

    /**
     * Set parent
     *
     * @param \Church\PlaceBundle\Entity\Place $parent
     * @return Place
     */
    public function setParent(\Church\PlaceBundle\Entity\Place $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Church\PlaceBundle\Entity\Place 
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Set city
     *
     * @param \Church\PlaceBundle\Entity\City $city
     * @return Place
     */
    public function setCity(\Church\PlaceBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Church\PlaceBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Place
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
     * @return Place
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

}