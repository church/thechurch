<?php

namespace Church\Response;

use Symfony\Component\HttpFoundation\Response;

trait SerializerResponseTrait
{

    /**
     * @var Symfony\Component\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * Reply action that serializes the data passed.
     */
    protected function reply(
        $data,
        string $format,
        int $status = 200,
        iterable $headers = [],
        array $context = [
          'groups' => [
            'api'
          ],
        ]
    ) : Response {

        return new Response(
            $this->serializer->serialize($data, $format, $context),
            $status,
            $headers
        );
    }
}
