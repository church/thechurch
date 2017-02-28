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
        $response = $this->client->get(null, [
            'query' => [
                'method' => 'whosonfirst.places.getInfo',
                'extras' => 'name:,wof:lang,wof:lang_x_official',
                'id' => $id,
            ],
        ]);

        return $this->serializer->deserialize((string) $response->getBody(), Place::class, 'json');
    }
}
