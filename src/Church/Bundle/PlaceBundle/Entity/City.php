<?php

namespace Church\Bundle\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Church\Bundle\PlaceBundle\Entity\Place;

/**
 * Church\Bundle\PlaceBundle\Entity\City
 *
 * @ORM\Entity(repositoryClass="Church\Bundle\PlaceBundle\Entity\CityRepository")
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
     * Set place
     *
     * @param Place $id
     * @return PlaceTitle
     */
    public function setPlace(Place $place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return Place
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

}
