<?php

namespace Church\PlaceBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Http\Exception\BadResponseException;
use Doctrine\ORM\EntityManager;

use Church\PlaceBundle\Entity\Place;
use Church\PlaceBundle\Entity\PlaceName;
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
      
      $boss = new Client('http://yboss.yahooapis.com');
      $oauth = new OauthPlugin(array(
          'consumer_key'    => $this->consumer_key,
          'consumer_secret' => $this->consumer_secret,
      ));
      
      $boss->addSubscriber($oauth);
      
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
          
        if ($db_place = $repository->find($current)) {
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
        $place->setType($item['type']);
        $place->setLatitude($item['latitude']);
        $place->setLongitude($item['longitude']);
        
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
        
        if ($item['type'] == 7) {
          $city = new City();
          $city->setPlace($place);
          $city->setSlug(strtolower($item['name']));
          $place->setCity($city);
        }
        
        $this->em->persist($place);
      }
      
      $this->em->flush();
    
      return $user_place;
      
    }
    
}
