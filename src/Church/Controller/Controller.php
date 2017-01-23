<?php

namespace Church\Controller;

use Church\Response\SerializerResponseTrait;
use Church\Entity\User\User;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Controller
{

    use SerializerResponseTrait;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
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

        if (!is_object($user = $token->getUser())) {
            throw new \Exception('Not Logged In.');
        }

        return $user;
    }

    /**
     * Deserialize and validate an object.
     *
     * This method exists to prevent validation from being skipped by mistake.
     */
    protected function deserialize(Request $request, string $type)
    {
        $object = $this->serializer->deserialize(
            $request->getContent(),
            $type,
            $request->getRequestFormat()
        );

        $this->validate($object);

        return $object;
    }

    protected function validate($data) : bool
    {
        $errors = $this->validator->validate($data);

        if (count($errors)) {
            throw new BadRequestHttpException((string) $errors);
        }

        return true;
    }
}
