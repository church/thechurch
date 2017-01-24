<?php

namespace Church\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Church\Entity\CityRepository
 *
 */
class CityRepository extends EntityRepository
{

    public function findCityBySlug($slug)
    {
          $query = $this->createQueryBuilder('c');
          $query->join('c.place', 'p');
          $query->join('p.name', 'n');
          $query->where('c.slug = :slug');
          $query->setParameter('slug', $slug);

          return $query->getQuery()->getSingleResult();
    }
}
