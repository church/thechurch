<?php

namespace Church\Bundle\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Church\Bundle\PlaceBundle\Entity\PlaceTitle
 *
 * @ORM\Entity
 * @ORM\Table(name="place_type")
 */
class PlaceType
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="place_type_id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

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
