<?php

namespace Church\Controller;

use Church\Response\SerializerResponseTrait;
use Church\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Controller
{

    use SerializerResponseTrait;

    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    public function __construct(
        SerializerInterface $serializer,
        TokenStorageInterface $tokenStorage,
        CsrfTokenManagerInterface $csrfTokenManager,
        ValidatorInterface $validator
    ) {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
        $this->csrfTokenManager = $csrfTokenManager;
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

    protected function validate($data) : bool
    {
        $errors = $this->validator->validate($data);

        if (count($errors)) {
            throw new BadRequestHttpException((string) $errors);
        }

        return true;
    }
}
