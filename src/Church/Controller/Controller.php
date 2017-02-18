<?php

namespace Church\Controller;

use Church\Entity\User\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * An abstract controller to extend.
 */
abstract class Controller
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
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Creates the Controller.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param RegistryInterface $doctrine
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Determine if current user is logged in.
     */
    protected function isLoggedIn() : bool
    {
        try {
            $this->getUser();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() : User
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            throw new \Exception('Not Logged In.');
        }

        $user = $token->getUser();

        if (!is_object($user)) {
            throw new \Exception('Not Logged In.');
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

        if ($this->isLoggedIn()) {
            $roles = array_merge($roles, $this->getUser()->getRoles());
        }

        return array_map(function ($role) use ($operation) {
            return $role . '_' . $operation;
        }, $roles);
    }

    /**
     * Deserialize and validate an object.
     *
     * This method exists to prevent validation from being skipped by mistake.
     *
     * @param Request $request
     * @param string|object $type
     * @param array $roles
     */
    protected function deserialize(Request $request, $type, array $roles = [])
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
     * Reply action that serializes the data passed.
     *
     * @param mixed $data
     * @param string $format
     * @param array $roles
     * @param int $status
     * @param array $headers
     */
    protected function reply(
        $data,
        string $format,
        array $roles = [],
        int $status = 200,
        array $headers = []
    ) : Response {

        $context = [
            'groups' => $this->getGroups(self::OPERATION_READ, $roles),
        ];
        return new Response(
            $this->serializer->serialize($data, $format, $context),
            $status,
            $headers
        );
    }
}
