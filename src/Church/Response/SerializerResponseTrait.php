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
     * @param array $groups
     * @param int $status
     * @param array $headers
     */
    protected function reply(
        $data,
        string $format,
        array $groups = ['public'],
        int $status = 200,
        array $headers = []
    ) : Response {

        $context = [
            'groups' => $groups,
        ];
        return new Response(
            $this->serializer->serialize($data, $format, $context),
            $status,
            $headers
        );
    }
}
