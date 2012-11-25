<?php

namespace Church\PlaceBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Church\PlaceBundle\Entity\Place;
use Church\PlaceBundle\Entity\PlaceTree;

class TreeMaker
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Place) {
          
          // Will this work?
          $repository = $entityManager->getRepository('AcmeStoreBundle:Product');
          
          
            
        }
    }
}