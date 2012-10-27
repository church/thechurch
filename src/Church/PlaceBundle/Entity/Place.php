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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
