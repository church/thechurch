<?php

namespace Church\PlaceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Church\PlaceBundle\Entity\PlaceRepository
 *
 */
class PlaceRepository extends EntityRepository
{

  public function findState($place_id)
  {
      $em = $this->getEntityManager();
      $query = $em->createQuery('SELECT p FROM ChurchPlaceBundle:Place p JOIN p.descendant t WHERE t.descendant = :place_id  AND p.type = 8')
      ->setParameter('place_id', $place_id);

      return $query->getSingleResult();
  }
  
  public function findCountry($place_id)
  {
      $em = $this->getEntityManager();
      $query = $em->createQuery('SELECT p FROM ChurchPlaceBundle:Place p JOIN p.descendant t WHERE t.descendant = :place_id AND p.type = 12')
      ->setParameter('place_id', $place_id);

      return $query->getSingleResult();
  }

  
}