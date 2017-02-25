<?php

namespace Church\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Church\Entity\PlaceRepository
 *
 */
class PlaceRepository extends EntityRepository
{

    public function findState($place_id)
    {
          $em = $this->getEntityManager();
          $query = $em->createQuery('SELECT p
                                     FROM Church:Place p
                                     JOIN p.descendant t
                                     WHERE t.descendant = :place_id
                                       AND p.type = 8')
                                       ->setParameter('place_id', $place_id);

          return $query->getSingleResult();
    }

    public function findCountry($place_id)
    {
          $em = $this->getEntityManager();
          $query = $em->createQuery('SELECT p
                                     FROM Church:Place p
                                     JOIN p.descendant t
                                     WHERE t.descendant = :place_id
                                        AND p.type = 12')
                                        ->setParameter('place_id', $place_id);

          return $query->getSingleResult();
    }
}
