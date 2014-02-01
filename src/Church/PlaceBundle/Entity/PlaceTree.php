<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @param \Church\PlaceBundle\Entity\Place $ancestor
     * @return PlaceTree
     */
    public function setAncestor(\Church\PlaceBundle\Entity\Place $ancestor)
    {
        $this->ancestor = $ancestor;
    
        return $this;
    }

    /**
     * Get ancestor
     *
     * @return \Church\PlaceBundle\Entity\Place 
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * Set descendant
     *
     * @param \Church\PlaceBundle\Entity\Place $descendant
     * @return PlaceTree
     */
    public function setDescendant(\Church\PlaceBundle\Entity\Place $descendant)
    {
        $this->descendant = $descendant;
    
        return $this;
    }

    /**
     * Get descendant
     *
     * @return \Church\PlaceBundle\Entity\Place 
     */
    public function getDescendant()
    {
        return $this->descendant;
    }
}