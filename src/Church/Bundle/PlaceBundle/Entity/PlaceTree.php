<?php

namespace Church\Bundle\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Church\Bundle\PlaceBundle\Entity\Place;

/**
 * @ORM\Entity
 * @ORM\Table(name="place_tree")
 */
class PlaceTree
{

  /**
   * @ORM\Id
   * @ORM\JoinColumn(name="ancestor", referencedColumnName="place_id")
   * @ORM\ManyToOne(targetEntity="Place")
   */
  private $ancestor;

  /**
   * @ORM\Id
   * @ORM\JoinColumn(name="descendant", referencedColumnName="place_id")
   * @ORM\ManyToOne(targetEntity="Place")
   */
  private $descendant;

  /**
   * @ORM\Column(type="integer")
   */
  private $depth;


    /**
     * Set depth
     *
     * @param integer $depth
     * @return PlaceTree
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set ancestor
     *
     * @param Place $ancestor
     * @return PlaceTree
     */
    public function setAncestor(Place $ancestor)
    {
        $this->ancestor = $ancestor;

        return $this;
    }

    /**
     * Get ancestor
     *
     * @return Place
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * Set descendant
     *
     * @param Place $descendant
     * @return PlaceTree
     */
    public function setDescendant(Place $descendant)
    {
        $this->descendant = $descendant;

        return $this;
    }

    /**
     * Get descendant
     *
     * @return Place
     */
    public function getDescendant()
    {
        return $this->descendant;
    }
}
