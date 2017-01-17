<?php

namespace Church\Controller;

use Church\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{

    /**
     * @var array
     */
    protected const SERIALIZE_CONTEXT = [
      'groups' => [
        'api'
      ],
    ];

    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var Symfony\Component\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * @var Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    public function __construct(
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * Reply action that serializes the data passed.
     */
    protected function reply(
        $data,
        string $format,
        int $status = 200,
        iterable $headers = []
    ) : Response {

        return new Response(
            $this->serializer->serialize($data, $format, self::SERIALIZE_CONTEXT),
            $status,
            $headers
        );
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() :? User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }
}
