<?php

namespace Church\Client;

use GuzzleHttp\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Search Client.
 */
abstract class AbstractClient
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
}
