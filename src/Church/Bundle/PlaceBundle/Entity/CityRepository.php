<?php

namespace Church\Bundle\PlaceBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Church\Bundle\PlaceBundle\Entity\CityRepository
 *
 */
class CityRepository extends EntityRepository
{

  public function findCityBySlug($slug)
  {
      $query = $this->createQueryBuilder('c');
      $query->join('c.place' ,'p');
      $query->join('p.name', 'n');
      $query->where('c.slug = :slug');
      $query->setParameter('slug', $slug);

      return $query->getQuery()->getSingleResult();
  }

}
