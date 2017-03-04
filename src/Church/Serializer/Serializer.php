<?php

namespace Church\Serializer;

use Church\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Deserializes a request body with validaiton.
 */
class Serializer implements SerializerInterface
{
    /**
     * @var string
     */
    protected const OPERATION_READ = 'read';

    /**
     * @var string
     */
    protected const OPERATION_WRITE = 'write';

    /**
     * @var array
     */
    protected const DEFAULT_ROLES = ['anonymous'];

    /**
     * @var SymfonySerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string
     */
    protected $corsOrigin;

    /**
     * Creates the Controller.
     *
     * @param SymfonySerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param TokenStorageInterface $tokenStorage
     * @param string $corsOrigin
     */
    public function __construct(
        SymfonySerializerInterface $serializer,
        ValidatorInterface $validator,
        TokenStorageInterface $tokenStorage,
        string $corsOrigin
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->tokenStorage = $tokenStorage;
        $this->corsOrigin = $corsOrigin;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = array())
    {
        $this->serializer->serialize($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, array $context = array())
    {
        $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() :? User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!is_object($user)) {
            return null;
        }

        return $user;
    }

    /**
     * Get the groups for the user.
     *
     * @param string $operation
     * @param array $roles
     */
    protected function getGroups(string $operation, array $roles = []) : array
    {
        $roles = array_merge(self::DEFAULT_ROLES, $roles);

        if ($user = $this->getUser()) {
            $roles = array_merge($roles, $user->getRoles());
        }

        return array_map(function ($role) use ($operation) {
            return $role . '_' . $operation;
        }, $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function request(Request $request, $type, array $roles = [])
    {

        if (!is_string($type) && !is_object($type)) {
            throw new \InvalidArgumentException('type must be a string or an object');
        }

        $object = null;
        if (is_object($type)) {
            $object = $type;
            $type = get_class($object);
        }

        if (!$request->getContent()) {
            throw new BadRequestHttpException('Missing Request Body.');
        }

        $object = $this->serializer->deserialize(
            $request->getContent(),
            $type,
            $request->getRequestFormat(),
            [
                'object_to_populate' => $object,
                'groups' => $this->getGroups(self::OPERATION_WRITE, $roles),
            ]
        );

        $this->validate($object);

        return $object;
    }

    /**
     * Validate an object.
     *
     * @param object $data
     */
    protected function validate($data) : bool
    {
        $errors = $this->validator->validate($data);

        if (count($errors)) {
            throw new BadRequestHttpException((string) $errors);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function respond(
        $data,
        string $format,
        array $roles = [],
        int $status = 200,
        array $headers = []
    ) : Response {

        $headers = array_merge([
            'Access-Control-Allow-Origin' => $this->corsOrigin,
        ], $headers);

        $context = [
            'groups' => $this->getGroups(self::OPERATION_READ, $roles),
            'enable_max_depth' => true,
        ];
        return new Response(
            $this->serializer->serialize($data, $format, $context),
            $status,
            $headers
        );
    }
}
