<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Church\PlaceBundle\Entity\Place
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Place
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="woeid", type="integer")
     * @ORM\Id
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
