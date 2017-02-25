<?php

namespace Church\Client\Mapzen;

use Church\Client\AbstractClient;
use Church\Entity\Location;

/**
 * Search Client.
 */
class Search extends AbstractClient implements SearchInterface
{

    /**
     * {@inheritdoc}
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
