<?php

namespace Church\Bundle\PlaceBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Church\Bundle\PlaceBundle\Entity\Place;
use Church\Budnle\PlaceBundle\Entity\PlaceTree;

class TreeMaker
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Place) {

          $em = $args->getEntityManager();
          $repository = $em->getRepository('ChurchPlaceBundle:PlaceTree');

          $tree = new PlaceTree();
          $tree->setAncestor($entity);
          $tree->setDescendant($entity);
          $tree->setDepth(0);
          $em->persist($tree);

          if ($parent = $entity->getParent()) {

            // Get the tree of the entity's parent.
            $parent_tree = $repository->findByDescendant($parent);

            foreach ($parent_tree as $tree) {
              $new_tree = clone $tree;
              $new_tree->setDescendant($entity);
              $new_tree->setDepth($tree->getDepth() + 1);
              $em->persist($new_tree);
            }

          }

          $em->flush();

        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

      $entity = $args->getEntity();

      if ($entity instanceof Place) {

        $em = $args->getEntityManager();
        $repository = $em->getRepository('ChurchPlaceBundle:Place');

        $original = $repository->find($entity->getId());

        $original_parent = $original->getParent();
        $new_parent = $entity->getParent();

        $original_parent_id = $original_parent->getId();
        $new_parent_id = $new_parent->getId();

        // Make sure there was a change in Parents before proceeding.
        if ($original_parent_id != $new_parent_id ) {

          $repository = $em->getRepository('ChurchPlaceBundle:PlaceTree');

          // First Get the comment's tree below itself.
          $descendants = $repository->findByAncestor($entity->getID());

          $descendant_ids = array();
          foreach ($descendants as $descendant) {
            $descendant_ids[] = $descendant->getId();
          }

          // Then delete the comment tree above the current comment.
          $qb = $em->createQueryBuilder('PlaceTree');
          $qb->remove();
          $qb->field('descendant')->in($descendant_ids);
          $qb->field('ancestor')->notIn($descendant_ids);
          $qb->getQuery();
          $qb->execute();

          // Finally, copy the tree from the new parent.
          $parent_tree = $repository->findByDescendant($new_parent);

          foreach ($parent_tree as $tree) {
            foreach ($descendants as $descendant) {
              $new_tree = clone $tree;
              $new_tree->setDescendant($descendant);
              $new_tree->setDepth($tree->getDepth() + $descendant->getDepth() + 1);
              $em->persist($new_tree);
            }
          }

          $em->flush();

        }

      }

    }

    public function preRemove(LifecycleEventArgs $args)
    {

      $entity = $args->getEntity();

      if ($entity instanceof Place) {

        $qb = $em->createQueryBuilder('PlaceTree');
        $qb->remove();
        $qb->addOr($qb->expr()->field('ancestor')->equals($place->getId()));
        $qb->addOr($qb->expr()->field('descendant')->equals($place->getId()));
        $qb->getQuery();
        $qb->execute();

      }
    }
}
