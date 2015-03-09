<?php

namespace Church\Client\Yahoo;

use GuzzleHTTP\ClientInterface;

class GeoPlanet
{

    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Gets the Guzzle HTTP Client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get Place Types
     *
     * @return array Place Types as defined by Yahoo! GeoPlanet
     */
    public function getPlaceTypes()
    {
        $response = $this->getClient()->get('placetypes');

        $types = $response->json();

        if (!empty($types['placeTypes']['placeType'])) {
            return $types['placeTypes']['placeType'];
        } else {
            return array();
        }
    }
}
