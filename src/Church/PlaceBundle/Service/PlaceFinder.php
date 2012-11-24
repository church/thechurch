<?php

namespace Church\PlaceBundle\Service;

use Church\PlaceBundle\Entity\Place;

class PlaceFinder {
        
    protected $consumer_key;
    
    protected $consumer_secret;
        
    public function __construct($consumer_key, $consumer_secret, $generic_appid)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->generic_appid = $generic_appid;
    }
    
    public function findPlace($controller, $query) {
    
      $params = array(
        'service' => 'placefinder',
        'ck' => $this->consumer_key,
        'secret' => $this->consumer_secret,
        'count' => 1,
        'q' => $query,
      );
  
      
      $conditions = array();
      foreach ($params as $key => $value) {
        $conditions[] = $key . '="' . addslashes($value) . '"';
      }
      
      $condition_string = implode(' AND ', $conditions);
      
      $yquery = "SELECT * FROM boss.search WHERE " . $condition_string;
      
      $url_params = array(
        'q' => $yquery,
        'format' => 'json',
        'env' => 'http://datatables.org/alltables.env',
      );
      
      $ch = curl_init('http://query.yahooapis.com/v1/public/yql?' . http_build_query($url_params));
      curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      
      $response = curl_exec($ch);
      
      $response = json_decode($response, TRUE);
                  
      if (!empty($response['query']['results']['bossresponse']['placefinder']['results']['result'])) {
        $result = $response['query']['results']['bossresponse']['placefinder']['results']['result'];
      }
      else {
        return FALSE;
      }
      
      // Create the Place
      $user_place = new Place();
      $user_place->setID($result['woeid']);
      $user_place->setType($result['woetype']);
      $user_place->setLatitude($result['latitude']);
      $user_place->setLongitude($result['longitude']);
      
      $params = array(
        'appid' => $this->generic_appid,
        'format' => 'json',
      );
      
      $endpoint = 'http://where.yahooapis.com/v1/place/' . $user_place->getID();
      $ch = curl_init($endpoint . '?' . http_build_query($params));
      curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      
      $response = curl_exec($ch);
      
      $response = json_decode($response, TRUE);
      
      if (empty($response['place']['woeid'])) {
        return $user_place;
      }
            
      $place = new Place();
      $place->setID($response['place']['woeid']);
      $place->setType($response['place']['placeTypeName attrs']['code']);
      $place->setLatitude($response['place']['centroid']['latitude']);
      $place->setLongitude($response['place']['centroid']['longitude']);
      
      $places = array();
      
      do {
        
        $endpoint = 'http://where.yahooapis.com/v1/place/' . $place->getID() . '/parent';
        $ch = curl_init($endpoint . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        $response = curl_exec($ch);
        
        $response = json_decode($response, TRUE);
        
        if (empty($response['place']['woeid'])) {
          $places[] = clone $place;
          $parent = FALSE;
          continue;
        }
        
        $endpoint = 'http://where.yahooapis.com/v1/place/' . $response['place']['woeid'];
        $ch = curl_init($endpoint . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        $response = curl_exec($ch);
        
        $response = json_decode($response, TRUE);
        
        if (empty($response['place']['woeid'])) {
          $places[] = clone $place;
          $parent = FALSE;
          continue;
        }
        
        $parent = new Place();
        $parent->setID($response['place']['woeid']);
        $parent->setName($response['place']['name']);
        $parent->setType($response['place']['placeTypeName attrs']['code']);
        $parent->setLatitude($response['place']['centroid']['latitude']);
        $parent->setLongitude($response['place']['centroid']['longitude']);
        
        $place->setParent(clone $parent);
        
        $places[] = clone $place;
                
        $place = clone $parent;
        
        $parent = NULL;
        
      } while ($parent !== FALSE);
            
      // Reverse the Array to start with the Highest Parent
      $places = array_reverse($places);
            
      $em = $controller->getDoctrine()->getManager();
      $repository = $controller->getDoctrine()->getRepository('Church\PlaceBundle\Entity\Place');
      
      // Save the Place
      foreach ($places as $place) {
        $existing = $repository->find($place->getID());
        if (empty($existing)) {
          $em->persist($place);
          $em->flush();
        }
      }
      	    
      return $user_place;
      
    }
    
}
