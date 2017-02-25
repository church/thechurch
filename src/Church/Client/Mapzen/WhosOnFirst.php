<?php

namespace Church\Client\Mapzen;

use Church\Client\AbstractClient;
use Church\Entity\Place\Place;

/**
 * Who's on First.
 */
class WhosOnFirst extends AbstractClient implements WhosOnFirstInterface
{

    /**
     * {@inheritdoc}
     */
    public function get(string $id) : Place
    {
        $path = implode('/', str_split($id, 3));
        $response = $this->client->get($path . '/' . $id . '.geojson');

        dump((string) $response->getBody());
        exit;

        return $this->serializer->deserialize((string) $response->getBody(), Place::class, 'json');
    }
}
