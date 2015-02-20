<?php

namespace Church\Utils;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Plugin\Backoff\BackoffPlugin;
use Doctrine\ORM\EntityManager;

use Church\Entity\Place;
use Church\Entity\PlaceName;
use Church\Entity\PlaceType;
use Church\Entity\City;

class PlaceFinder {

    protected $em;

    protected $boss;

    protected $geo;


    public function __construct(EntityManager $em, $settings)
    {

        // @TODO oh dear god.. this is such a bad class... what was I thinking?
        $this->em = $em;

        $this->boss = new Client('http://yboss.yahooapis.com');
        $oauth = new OauthPlugin(array(
            'consumer_key'    => $settings['consumer_key'],
            'consumer_secret' => $settings['consumer_secret'],
        ));
        $this->boss->addSubscriber($oauth);
        $backoff = BackoffPlugin::getExponentialBackoff();
        $this->boss->addSubscriber($backoff);

        $this->geo = new Client('http://where.yahooapis.com/v1?format=json&appid='.$settings['generic_appid']);

    }

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

    public function getTypes()
    {

      $request = $this->geo->get('placetypes');

      $response = $request->send();

      $types = $response->json();

      $types = !empty($types['placeTypes']['placeType']) ? $types['placeTypes']['placeType'] : array();

      return $types;

    }

    public function saveTypes($types = array())
    {

       foreach ($types as $type) {
        if (!empty($type['placeTypeName']) && !empty($type['placeTypeName attrs']['code'])) {
          $place_type = new PlaceType();
          $place_type->setID($type['placeTypeName attrs']['code']);
          $place_type->setName($type['placeTypeName']);
          $this->em->persist($place_type);
        }
       }

       $this->em->flush();

       return TRUE;

    }

    public function makeSlug($str)
    {
      $slug = trim($str);
      $slug = strtolower($slug);
      $slug = str_replace(' ', '-', $slug);
      $slug = str_replace('.', '', $slug);

      return $slug;
    }

}
