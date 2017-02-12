<?php

namespace Church\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Deserialize Request Trait.
 */
trait DeserializeRequestTrait
{

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Deserialize and validate an object.
     *
     * This method exists to prevent validation from being skipped by mistake.
     *
     * @param Request $request
     * @param string $type
     */
    protected function deserialize(Request $request, string $type)
    {

        if (!$request->getContent()) {
            throw new BadRequestHttpException('Missing Request Body.');
        }

        $object = $this->serializer->deserialize(
            $request->getContent(),
            $type,
            $request->getRequestFormat()
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
}
