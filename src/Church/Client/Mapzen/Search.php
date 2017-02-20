<?php

namespace Church\Client\Mapzen;

use Church\Entity\Location;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Search Client.
 */
class Search implements SearchInterface
{

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Create a new Search client.
     *
     * @param ClientInterface $client
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ClientInterface $client,
        SerializerInterface $serializer
    ) {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * Get a place by id.
     *
     * @param string $id
     */
    public function get(string $id) : Location
    {
        $response = $this->client->get('place', [
            'query' => [
                'ids' => $id
            ],
        ]);

        // @TODO convert the denomrlaizer in the controller to a custom
        //       service.
        return $this->serializer->deserialize((string) $response->getBody(), Location::class, 'json');
    }
}
