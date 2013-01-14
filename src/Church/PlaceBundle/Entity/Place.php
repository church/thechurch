<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Church\PlaceBundle\Entity\Place
 *
 * @ORM\Entity
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="Church\PlaceBundle\Entity\PlaceRepository")
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
     * @ORM\OneToMany(targetEntity="PlaceTree", mappedBy="descendant")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="descendant")
     */
    private $ancestor;
    
    /**
     * @ORM\OneToMany(targetEntity="PlaceTree", mappedBy="ancestor")
     * @ORM\JoinColumn(name="place_id", referencedColumnName="ancestor")
     */
    private $descendant;
    
    /**
     * @ORM\ManyToOne(targetEntity="PlaceType")
     * @ORM\JoinColumn(name="place_type_id", referencedColumnName="place_type_id")
     */
    private $type;
    
    /**
     * @ORM\OneToMany(targetEntity="PlaceName", mappedBy="place", cascade={"all"})
     */
    private $name;
    
    /**
     * @ORM\OneToOne(targetEntity="City", mappedBy="place", cascade={"all"})
     */
    private $city;
    
    /**
     * @ORM\Column(type="decimal", precision=8, scale=6, nullable=true)
     */
    private $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;
    
    
    /**
     * Construct
     */
    public function __construct()
    {
        $this->name = new ArrayCollection();
        $this->ancestor = new ArrayCollection();
        $this->descendant = new ArrayCollection();
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
     * Add ancestor
     *
     * @param \Church\PlaceBundle\Entity\PlaceTree $ancestor
     * @return Place
     */
    public function addAncestor(\Church\PlaceBundle\Entity\PlaceTree $ancestor)
    {
        $this->ancestor[] = $ancestor;
    
        return $this;
    }

    /**
     * Remove ancestor
     *
     * @param \Church\PlaceBundle\Entity\PlaceTree $ancestor
     */
    public function removeAncestor(\Church\PlaceBundle\Entity\PlaceTree $ancestor)
    {
        $this->ancestor->removeElement($ancestor);
    }

    /**
     * Get ancestor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }
    
    /**
     * Add descendant
     *
     * @param \Church\PlaceBundle\Entity\PlaceTree $descendant
     * @return Place
     */
    public function addDescendant(\Church\PlaceBundle\Entity\PlaceTree $descendant)
    {
        $this->descendant[] = $descendant;
    
        return $this;
    }

    /**
     * Remove descendant
     *
     * @param \Church\PlaceBundle\Entity\PlaceTree $descendant
     */
    public function removeDescendant(\Church\PlaceBundle\Entity\PlaceTree $descendant)
    {
        $this->descendant->removeElement($descendant);
    }

    /**
     * Get descendant
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDescendant()
    {
        return $this->descendant;
    }
    
    /**
     * Set type
     *
     * @param \Church\PlaceBundle\Entity\PlaceType $type
     * @return Place
     */
    public function setType(\Church\PlaceBundle\Entity\PlaceType $type = null)
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