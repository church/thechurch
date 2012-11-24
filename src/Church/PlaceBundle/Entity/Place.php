<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\JoinColumn(name="parent", referencedColumnName="place_id")
     * @ORM\ManyToOne(targetEntity="Place")
     */
    private $parent;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $type;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;
    
    /**
     * @ORM\Column(type="decimal", precision=8, scale=6)
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=9, scale=6)
     */
    private $longitude;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Place
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Place
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