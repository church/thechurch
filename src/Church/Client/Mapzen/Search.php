<?php

namespace Church\Client\Mapzen;

use Church\Client\AbstractClient;
use Church\Entity\Location;
use GuzzleHttp\Exception\ClientException;

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
        $response = null;
        while (!$response) {
            try {
                $response = $this->client->get('place', [
                    'query' => [
                        'ids' => $id
                    ],
                ]);
            } catch (ClientException $e) {
                // Wait a second and try again.
                if ($e->getResponse()->getStatusCode() == 429) {
                    $response = null;
                    sleep(1);
                    continue;
                }

                throw $e;
            }
        }

        // @TODO convert the denomrlaizer in the controller to a custom
        //       service.
        return $this->serializer->deserialize((string) $response->getBody(), Location::class, 'json');
    }
}
