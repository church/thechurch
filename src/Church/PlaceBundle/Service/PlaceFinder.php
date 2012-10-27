<?php

namespace Church\PlaceBundle\Service;

class PlaceFinder {

    protected $method;
    
    protected $endpoint;
            
    protected $oauth;    
    
    protected $params;
    
    public function __construct($app_id, $consumer_key)
    {
        $this->method = 'GET';
        $this->endpoint = 'http://yboss.yahooapis.com/geo/placefinder';
        $this->oauth = array(
          'oauth_version' => '1.0',
          'oauth_nonce' => $md5(uniqid(null, true)),
          'oauth_timestamp' => time(),
          'oauth_consumer_key' => $consumer_key,
          'oauth_signature_method' => 'HMAC-SHA1',
        );
        $this->params = array(
          'appid' => $app_id,
        );
    }
    
    public function findPlace($query) {
      $this->params['q'] = $query;
      
      $this->generateAuthorizatio();
      
    }
    
    private function generateOAuth() {
      
    }
    
    private function generateAuthorization() {
      $this->generateSignature();
    }

    private function generateSignature()
    {
      $params = $this->params;
      $params += $this->oauth;
      
      $encoded = array();
      foreach ($params as $key => $value)
        $encoded[rawurlencode($key)] = rawurlencode($value);
      }
      
      
      
    }
}
