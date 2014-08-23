<?php

namespace Church\Bundle\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Church\Bundle\PlaceBundle\Entity\PlaceName;
use Church\Bundle\PlaceBundle\Entity\PlaceTree;
use Church\Bundle\PlaceBundle\Entity\Place;
use Church\Bundle\PlaceBundle\Entity\City;

/**
 * Church\Bundle\PlaceBundle\Entity\Place
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="Church\Bundle\PlaceBundle\Entity\PlaceRepository")
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
     * @ORM\Column(type="datetime")
     */
    private $created;


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
     * @param PlaceName $name
     * @return Place
     */
    public function addName(PlaceName $name)
    {
        $this->name[] = $name;

        return $this;
    }

    /**
     * Remove name
     *
     * @param PlaceName $name
     */
    public function removeName(PlaceName $name)
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
     * @param PlaceTree $ancestor
     * @return Place
     */
    public function addAncestor(PlaceTree $ancestor)
    {
        $this->ancestor[] = $ancestor;

        return $this;
    }

    /**
     * Remove ancestor
     *
     * @param PlaceTree $ancestor
     */
    public function removeAncestor(PlaceTree $ancestor)
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
     * @param PlaceTree $descendant
     * @return Place
     */
    public function addDescendant(PlaceTree $descendant)
    {
        $this->descendant[] = $descendant;

        return $this;
    }

    /**
     * Remove descendant
     *
     * @param PlaceTree $descendant
     */
    public function removeDescendant(PlaceTree $descendant)
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
     * @param PlaceType $type
     * @return Place
     */
    public function setType(PlaceType $type = null)
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
     * @param Place $parent
     * @return Place
     */
    public function setParent(Place $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Place
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return Place
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
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
