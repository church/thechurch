<?php

namespace Church\Serializer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

/**
 * Deserializes a request body with validaiton.
 */
interface SerializerInterface extends SymfonySerializerInterface
{

    /**
     * Deserialize and validate a request.
     *
     * This method exists to prevent validation from being skipped by mistake.
     *
     * @param Request $request
     * @param string|object $type
     * @param array $roles
     */
    public function request(Request $request, $type, array $roles = []);

    /**
     * Reply action that serializes the data passed.
     *
     * @param mixed $data
     * @param string $format
     * @param array $roles
     * @param int $status
     * @param array $headers
     */
    public function respond(
        $data,
        string $format,
        array $roles = [],
        int $status = 200,
        array $headers = []
    ) : Response;
}
