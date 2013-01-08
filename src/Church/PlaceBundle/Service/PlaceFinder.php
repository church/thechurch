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
      $names = array();
      
      do {
        
        $db_place = $repository->find($place->getID());
        
        if ($db_place) {
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
        $parent->setType($data['place']['placeTypeName attrs']['code']);
        $parent->setLatitude($data['place']['centroid']['latitude']);
        $parent->setLongitude($data['place']['centroid']['longitude']);
        
        $lang = explode('-', $data['place']['lang']);
        
        $names[$data['place']['woeid']]['name'] = $data['place']['name'];
        $names[$data['place']['woeid']]['language'] = $lang[0];
        $names[$data['place']['woeid']]['country'] = $lang[1];
        
        $place->setParent(clone $parent);
        
        $places[] = clone $place;
                
        $place = clone $parent;
        
        $parent = NULL;
        
      } while ($parent !== FALSE);
            
      // Reverse the Array to start with the Highest Parent
      $places = array_reverse($places);
      
      // Save the Place
      foreach ($places as $place) {
        $this->em->merge($place); // This has to be a merge or an exception is thrown TODO: Figure out how to do this wihtout the merge.
      }
      
      $this->em->flush();
      
      // Ideally, you should be able to save the name with the
      // Place, however, you can't seem to do this without
      // throwing an exception. By the same token, you
      // also can't query for all of the places
      // The solution below is the only solution that
      // has been found thus far.
      foreach ($names as $id => $name_array) {
          $place = $repository->findOneById($id);
          $name = new PlaceName();
          $name->setPlace($place);
          $name->setName($name_array['name']);
          $name->setLanguage($name_array['language']);
          $name->setCountry($name_array['country']);
          $this->em->merge($name); // Merge does not work so well with duplicate data
          
          if ($place->getType() == 7) {
            $city = new City();
            $city->setPlace($place);
            $city->setSlug(strtolower($name->getName()));
            $this->em->merge($city); // Merge does not work so well with duplicate data
          }
      }
      
      $this->em->flush();
        
            	    
      return $user_place;
      
    }
    
}
