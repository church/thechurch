<?php

namespace Church\Client\Yahoo;

use GuzzleHTTP\ClientInterface;
use GuzzleHttp\Event\SubscriberInterface;

class PlaceFinder
{

  protected $boss;

  protected $yql;

  public function __construct(ClientInterface $boss,
                              ClientInterface $yql,
                              SubscriberInterface $oauth)
  {

    $this->boss = $boss;
    $this->boss->getEmitter()->attach($oauth);

    $this->yql = $yql;
    $this->yql->getEmitter()->attach($oauth);
  }

  /**
   * Gets the BOSS Guzzle HTTP Client
   *
   * @return ClientInterface
   */
  public function getBOSS() {
    return $this->boss;
  }

  /**
   * Gets the YQL Guzzle HTTP Client
   *
   * @return ClientInterface
   */
  public function getYQL() {
    return $this->yql;
  }

  /**
   * Query for a place by Latitude & Longitude.
   *
   * @return array An array of results.
   */
  public function findByLatitudeLongitude($latitude, $longitude) {

    $text = $latitude.','.$longitude;
    $query = sprintf('select * from geo.placefinder where text="%s" and gflags="R"', $text);

    $result = $this->getYQL()->get(NULL, array(
      'query' => array(
        'q' => $query,
      ),
    ));

    // @TODO if a query is not returned from YQL, try BOSS.

    // @TODO this should return an array of results, rather than all the Yahoo!
    //       garbage. 
    return $result->json();

  }

}
