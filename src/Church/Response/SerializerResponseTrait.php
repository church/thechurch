<?php

namespace Church\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Serializer Response Trait.
 */
trait SerializerResponseTrait
{

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Reply action that serializes the data passed.
     *
     * @param mixed $data
     * @param string $format
     * @param int $status
     * @param array $headers
     * @param array $context
     */
    protected function reply(
        $data,
        string $format,
        int $status = 200,
        array $headers = [],
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
