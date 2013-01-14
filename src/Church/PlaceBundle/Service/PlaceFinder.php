<?php

namespace Church\PlaceBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Plugin\Backoff\BackoffPlugin;
use Doctrine\ORM\EntityManager;

use Church\PlaceBundle\Entity\Place;
use Church\PlaceBundle\Entity\PlaceName;
use Church\PlaceBundle\Entity\PlaceType;
use Church\PlaceBundle\Entity\City;

class PlaceFinder {
    
    protected $em;
    
    protected $consumer_key;
    
    protected $consumer_secret;
    
    protected $generic_appid;
        
    public function __construct(EntityManager $em, $consumer_key, $consumer_secret, $generic_appid)
    {
        $this->em = $em;
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->generic_appid = $generic_appid;
    }
    
    public function findPlace($query) {
    
      $repository = $this->em->getRepository('Church\PlaceBundle\Entity\Place');
      $city_repository = $this->em->getRepository('Church\PlaceBundle\Entity\City');
      $type_repository = $this->em->getRepository('Church\PlaceBundle\Entity\PlaceType');
      
      $boss = new Client('http://yboss.yahooapis.com');
      $oauth = new OauthPlugin(array(
          'consumer_key'    => $this->consumer_key,
          'consumer_secret' => $this->consumer_secret,
      ));
      
      $boss->addSubscriber($oauth);
      
      $backoff = BackoffPlugin::getExponentialBackoff();

      $boss->addSubscriber($backoff);
            
      $request = $boss->get('geo/placefinder?count=1&flags=J&q='.$query);
      
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
        return FALSE;
      }
      
      // Create the Place
      $user_place = new Place();
      $user_place->setID($result['woeid']);
      $user_place->setLatitude($result['latitude']);
      $user_place->setLongitude($result['longitude']);
      
      $geo = new Client('http://where.yahooapis.com/v1/place?format=json&appid='.$this->generic_appid);
      
      $items = array();
      $current = $result['woeid'];
      
      do {
          
        if ($repository->find($current)) {
          break;
        }
      
        $request = $geo->get($current);
      
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
        
        $request = $geo->get($current . '/parent');
        
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
      
      // Save the Place
      foreach ($items as $id => $item) {
      
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
      
      foreach ($items as $id => $item) {
      
        if ($item['type'] == 7 && !$city_repository->find($id) && $place = $repository->find($id)) {
                    
          $city = new City();
          $city->setPlace($place);
          
          // Prepare the Name.
          $slug = trim($item['name']);
          $slug = strtolower($slug);
          $slug = str_replace(' ', '-', $slug);
          $slug = str_replace('.', '', $slug);
          
          // If the City slug already exists, create a new one
          if ($city_repository->findOneBySlug($slug)) {
            
            if ($state = $repository->findState($place->getID())) {
              if ($state_name = $state->getName()->first()->getName()) {
                $state_slug = trim($state_name);
                $state_slug = strtolower($state_slug);
                $state_slug = str_replace(' ', '-', $state_slug);
                $state_slug = str_replace('.', '', $state_slug);
                $slug .= '-'.$state_slug;
              }
            }
            
          } 
        
          // Make sure that this name doesn't exist
          if ($city_repository->findOneBySlug($slug)) {
            
            if ($country = $repository->findCountry($place->getID())) {
              if ($country_name = $country->getName()->first()->getName()) {
                $country_slug = trim($country_name);
                $country_slug = strtolower($country_slug);
                $country_slug = str_replace(' ', '-', $country_slug);
                $country_slug = str_replace('.', '', $country_slug);
                $slug .= '-'.$country_slug;
              }
            }
            
          }
          
          $city->setSlug($slug);
          
          $this->em->persist($city);
          
        }
        
      }
      
      $this->em->flush();
    
      return $user_place;
      
    }
    
    public function getTypes()
    {
    
       $geo = new Client('http://where.yahooapis.com/v1?format=json&appid='.$this->generic_appid);
       
       $request = $geo->get('placetypes');
        
        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            return FALSE;
        }
        
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
    
}
