<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Patchwork\Utf8;

use Church\Client\Yahoo\GeoPlanet as YahooGeoPlanet;
use Church\Client\Yahoo\PlaceFinder as YahooPlaceFinder;
use Church\Entity\Place;
use Church\Entity\PlaceName;
use Church\Entity\PlaceType;
use Church\Entity\City;

class PlaceFinder
{

    protected $doctrine;

    protected $geo;

    protected $finder;

    protected $slug;


    public function __construct(
        RegistryInterface $doctrine,
        YahooGeoPlanet $geo,
        YahooPlaceFinder $finder,
        Slug $slug
    ) {

        // @TODO oh dear god.. this is such a bad class... what was I thinking?

        $this->doctrine = $doctrine;

        $this->geo = $geo;

        $this->finder = $finder;

        $this->slug = $slug;
    }

    /**
     * Get Doctrine
     *
     * @return RegistryInterface
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * Get GeoPlanet
     *
     * @return YahooGeoPlanet
     */
    public function getGeoPlanet()
    {
        return $this->geo;
    }

    /**
     * Get Finder
     *
     * @return YahooPlaceFinder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * Get Slug
     *
     * @return Slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /*
    public function findSavePlace($query) {

      $result = $this->findPlace($query);
      $places = $this->findNewPlaceTree($result['woeid']);
      $this->savePlaces($places);
      $this->saveCities($places);

      return $result;

    }

    public function findPlace($query) {

      $repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\Place');
      $city_repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\City');
      $type_repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\PlaceType');

      // @TODO maybe there is no need to access the client directly unless needed?
      // i.e. the class could have a "query" method that just accepts the query
      // string.
      $request = $this->boss->get('geo/placefinder?count=1&flags=J&q='.$query);

      try {
          $response = $request->send();
      } catch (BadResponseException $e) {
          return FALSE;
      }

      $data = $response->json();

      if (!empty($data['bossresponse']['placefinder']['results'][0])) {
        $result = $data['bossresponse']['placefinder']['results'][0];
      }
      else {
        return array();
      }

      return $result;

    }

    public function findNewPlaceTree($place_id)
    {

      $repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\Place');

      $items = array();
      $current = $place_id;

      do {

        if ($repository->find($current)) {
          break;
        }

        $request = $this->geo->get('place/'.$current);

        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            break;
        }

        $data = $response->json();

        if (empty($data['place']['woeid'])) {
          break;
        }

        $lang = explode('-', $data['place']['lang']);

        $items[$data['place']['woeid']] = array(
          'type' => $data['place']['placeTypeName attrs']['code'],
          'latitude' => $data['place']['centroid']['latitude'],
          'longitude' => $data['place']['centroid']['longitude'],
          'name' => $data['place']['name'],
          'language' => $lang[0],
          'country' => $lang[1],
        );

        $request = $this->geo->get('place/'.$current.'/parent');

        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            break;
        }

        $parent = $response->json();

        if (empty($parent['place']['woeid'])) {
          break;
        }

        $items[$data['place']['woeid']]['parent'] = $parent['place']['woeid'];

        $current = $parent['place']['woeid'];

      } while (!empty($current));

      // Reverse the Array to start with the Highest Parent
      $items = array_reverse($items, TRUE);

      return $items;

    }

    public function savePlaces($places = array())
    {

      $repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\Place');
      $type_repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\PlaceType');

      foreach ($places as $id => $item) {

        $place = new Place();
        $place->setID($id);

        if (!empty($item['latitude'])) {
          $place->setLatitude($item['latitude']);
        }
        if (!empty($item['longitude'])) {
          $place->setLongitude($item['longitude']);
        }

        if ($type = $type_repository->find($item['type'])) {
          $place->setType($type);
        }

        if (!empty($item['parent']) && $parent = $repository->find($item['parent'])) {
          $place->setParent($parent);
        }

        if ($item['type'] != 11 && !empty($item['name'])) {
          $name = new PlaceName();
          $name->setPlace($place);
          $name->setLanguage($item['language']);
          $name->setCountry($item['country']);
          $name->setName($item['name']);
          $place->addName($name);
        }

        $this->em->persist($place);

      }

      $this->em->flush();

    }

    public function saveCities($places = array())
    {

      $repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\Place');
      $city_repository = $this->em->getRepository('Church\Bundle\PlaceBundle\Entity\City');

      foreach ($places as $id => $item) {

        if ($item['type'] == 7 && !$city_repository->find($id) && $place = $repository->find($id)) {

          $city = new City();
          $city->setPlace($place);

          // Prepare the Name.
          $slug = $this->getSlug()->create($item['name']);

          // If the City slug already exists, create a new one
          if ($city_repository->findOneBySlug($slug)) {

            if ($state = $repository->findState($place->getID())) {
              if ($state_name = $state->getName()->first()->getName()) {
                $slug .= '-'.$this->getSlug()->create($state_name);
              }
            }

          }

          // Make sure that this name doesn't exist
          if ($city_repository->findOneBySlug($slug)) {

            if ($country = $repository->findCountry($place->getID())) {
              if ($country_name = $country->getName()->first()->getName()) {
                $slug .= '-'.$this->getSlug()->create($country_name);
              }
            }

          }

          $city->setSlug($slug);

          $this->em->persist($city);

        }

      }

      $this->em->flush();

    }
    */

    /**
     * Get all Place Types.
     *
     * @return array List of PlaceTypes
     */
    public function getPlaceTypes()
    {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('Church\Entity\PlaceType');

        if ($types = $repository->findAll()) {
            return $types;
        }

        foreach ($this->getGeoPlanet()->getPlaceTypes() as $type) {
            if (empty($type['placeTypeName'])) {
                continue;
            }

            if (empty($type['placeTypeName attrs']['code'])) {
                continue;
            }

            $place_type = new PlaceType();
            $place_type->setID($type['placeTypeName attrs']['code']);
            $place_type->setName($type['placeTypeName']);
            $em->persist($place_type);
        }

        $em->flush();

        return $repository->findAll();
    }
}
