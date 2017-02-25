<?php

namespace Church\Utils;

use Church\Client\Mapzen\SearchInterface;
use Church\Client\Mapzen\WhosOnFirstInterface;
use Church\Entity\Location;
use Church\Entity\Place\Place;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Place Finder.
 */
class PlaceFinder implements PlaceFinderInterface
{

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var SearchInterface
     */
    protected $search;

    /**
     * @var WhosOnFirstInterface
     */
    protected $whosonfirst;

    /**
     * @var SlugInterface
     */
    protected $slug;

    /**
     * Creates a Place Finder.
     *
     * @param RegistryInterface $doctrine
     * @param SearchInterface $search
     * @param WhosOnFirstInterface $whosonfirst
     * @param SlugInterface $slug
     */
    public function __construct(
        RegistryInterface $doctrine,
        SearchInterface $search,
        WhosOnFirstInterface $whosonfirst,
        SlugInterface $slug
    ) {

        $this->doctrine = $doctrine;
        $this->search = $search;
        $this->whosonfirst = $whosonfirst;
        $this->slug = $slug;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Location $input) : Location
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(Location::class);

        // Get all of the details from Mapzen.
        $input = $this->search->get($input->getId());

        // Check to see if a location already exists.
        if ($location = $repository->find($input->getId())) {
            // If a location already exists and the places match, then there
            // isn't anything left to do.
            if ($location->getPlace() && $location->getPlace()->getId() === $input->getPlace()->getId()) {
                return $location;
            }
        } else {
            $location = new Location([
                'id' => $input->getId(),
                'latitude' => $input->getLatitude(),
                'longitude' => $input->getLongitude(),
            ]);
            $em->persist($location);
            $em->flush();
        }

        $place = $this->getPlace($input->getPlace());

        $location->setPlace($place);
        $em->flush();

        return $location;
    }

    /**
     * Gets a Place
     *
     * @param Place $input
     */
    protected function getPlace(Place $input) : Place
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(Place::class);
        $input = $this->whosonfirst->get($input->getId());

        $place = $repository->find($input->getId());
        if ($place = $repository->find($input->getId())) {
            if (!$place->getParent() && !$input->getParent()) {
                return $place;
            }

            if ($place->getParent() && $place->getParent()->getId() === $input->getParent()->getId()) {
                return $place;
            }
        }

        $parent = null;
        if ($input->getParent()) {
            $parent = $this->getPlace($input->getParent());
        }

        $place = new Place([
            'id' => $input->getId(),
            'parent' => $parent,
            'slug' => $this->slug->create($input->getName()),
            'name' => $input->getName(),
        ]);

        $em->persist($place);
        $em->flush();

        return $place;
    }
}
