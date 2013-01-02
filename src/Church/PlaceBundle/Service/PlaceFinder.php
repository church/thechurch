<?php

namespace Church\PlaceBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Guzzle\Http\Exception\BadResponseException;

use Church\PlaceBundle\Entity\Place;

class PlaceFinder {
        
    protected $consumer_key;
    
    protected $consumer_secret;
    
    protected $generic_appid;
        
    public function __construct($consumer_key, $consumer_secret, $generic_appid)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->generic_appid = $generic_appid;
    }
    
    public function findPlace($em, $query) {
    
      $repository = $em->getRepository('Church\PlaceBundle\Entity\Place');
      
      $boss = new Client('http://yboss.yahooapis.com/geo?count=1&flags=J');
      $oauth = new OauthPlugin(array(
          'consumer_key'    => $this->consumer_key,
          'consumer_secret' => $this->consumer_secret,
      ));
      
      $boss->addSubscriber($oauth);
      
      $request = $boss->get('placefinder');
      $request->getQuery()->set('q', $query);
      
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
      
      $request = $geo->get($user_place->getID());
      
      try {
          $response = $request->send();
      } catch (BadResponseException $e) {
          return $user_place;
      }
      
      $data = $response->json();
      
      if (empty($data['place']['woeid'])) {
        return $user_place;
      }
            
      $place = new Place();
      $place->setID($data['place']['woeid']);
      $place->setType($data['place']['placeTypeName attrs']['code']);
      $place->setLatitude($data['place']['centroid']['latitude']);
      $place->setLongitude($data['place']['centroid']['longitude']);
      
      $places = array();
      
      do {
        
        $db_place = $repository->find($place->getID());
        
        if (!empty($db_place)) {
          $parent = FALSE;
          break;
        }
        
        $request = $geo->get($place->getID() . '/parent');
        
        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            $places[] = clone $place;
            $parent = FALSE;
            continue;
        }

        $data = $response->json();
        
        if (empty($data['place']['woeid'])) {
          $places[] = clone $place;
          $parent = FALSE;
          continue;
        }
        
        $request = $geo->get($data['place']['woeid']);
        
        try {
            $response = $request->send();
        } catch (BadResponseException $e) {
            $places[] = clone $place;
            $parent = FALSE;
            continue;
        }
        
        $data = $response->json();
        
        if (empty($data['place']['woeid'])) {
          $places[] = clone $place;
          $parent = FALSE;
          continue;
        }
        
        $parent = new Place();
        $parent->setID($data['place']['woeid']);
        $parent->setName($data['place']['name']);
        $parent->setType($data['place']['placeTypeName attrs']['code']);
        $parent->setLatitude($data['place']['centroid']['latitude']);
        $parent->setLongitude($data['place']['centroid']['longitude']);
        
        $place->setParent(clone $parent);
        
        $places[] = clone $place;
                
        $place = clone $parent;
        
        $parent = NULL;
        
      } while ($parent !== FALSE);
            
      // Reverse the Array to start with the Highest Parent
      $places = array_reverse($places);
      
      // Save the Place
      foreach ($places as $place) {
        $em->merge($place);
      }
      
      $em->flush();
      	    
      return $user_place;
      
    }
    
}
