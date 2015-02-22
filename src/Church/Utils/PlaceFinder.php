<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;

use Church\Client\Yahoo\GeoPlanet;
use Church\Entity\Place;
use Church\Entity\PlaceName;
use Church\Entity\PlaceType;
use Church\Entity\City;

class PlaceFinder {

    protected $doctrine;

    protected $boss;

    protected $geo;


    public function __construct(RegistryInterface $doctrine,
                                GeoPlanet $geo)
    {

        // @TODO oh dear god.. this is such a bad class... what was I thinking?
        // instead of injecting a settings array, instead inject
        // ready-to-go services.

        // @TODO we've made a little progress on this class, it's less aweful
        //       than it used to be.

        // @TODO This is correct, but do we need it?
        // I guess at the end of the day we should return an entity object,
        // and regardless, it should be from the database.
        $this->doctrine = $doctrine;

        $this->geo = $geo;
        /*
        // @TODO this is aweful... we should create a new service, but should that
        // service inject the Client or extend it?
        $this->boss = new Client('http://yboss.yahooapis.com');

        // @TODO We'll need a factory to handle the oAuth...
                 actually a new service should do the trick.
        $oauth = new OauthPlugin(array(
            'consumer_key'    => $settings['consumer_key'],
            'consumer_secret' => $settings['consumer_secret'],
        ));

        // @TODO but this is the part that is weird... I wonder if this should
                 happen in a factory or it's ok to happen in the constructor.
        $this->boss->addSubscriber($oauth);

        // @TODO I don't think this is needed in the newest version of Guzzle.
        $backoff = BackoffPlugin::getExponentialBackoff();
        $this->boss->addSubscriber($backoff);

        // @TODO this is aweful... we should create a new service, but should that
        // service inject the Client or extend it?
        // ... I'm going to say we should inject it, and we should add methods
        // as needed.
        $this->geo = new Client('http://where.yahooapis.com/v1?format=json&appid='.$settings['generic_appid']);
        */

    }

    /**
     * Get Doctrine
     *
     * @return RegistryInterface
     */
    public function getDoctrine() {
      return $this->doctrine;
    }

    /**
     * Get GeoPlanet
     *
     * @return GeoPlanet
     */
    public function getGeoPlanet() {
      return $this->geo;
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
          $slug = $this->makeSlug($item['name']);

          // If the City slug already exists, create a new one
          if ($city_repository->findOneBySlug($slug)) {

            if ($state = $repository->findState($place->getID())) {
              if ($state_name = $state->getName()->first()->getName()) {
                $slug .= '-'.$this->makeSlug($state_name);
              }
            }

          }

          // Make sure that this name doesn't exist
          if ($city_repository->findOneBySlug($slug)) {

            if ($country = $repository->findCountry($place->getID())) {
              if ($country_name = $country->getName()->first()->getName()) {
                $slug .= '-'.$this->makeSlug($country_name);
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

    /**
     * Generate a Slug.
     *
     * @param string $str Name of a place to be slugged.
     *
     * @return string Ready to use slug.
     */
    public function makeSlug($str)
    {
      $slug = trim($str);
      $slug = strtolower($slug);
      $slug = str_replace(' ', '-', $slug);
      $slug = str_replace('.', '', $slug);

      return $slug;
    }

}
