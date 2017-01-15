<?php

namespace Church\Controller;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Reply action that serializes the data passed.
     */
    public function reply(
        $data,
        string $format,
        int $status = 200,
        iterable $headers = []
    ) : Response {

        return new Response(
            $this->serializer->serialize($data, $format),
            $status,
            $headers
        );
    }
}
