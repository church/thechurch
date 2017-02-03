<?php

namespace Church\Normalizer;

use Church\Entity\User\Login;
use Church\Validator\Constraints\LoginValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LoginNormalizer implements DenormalizerInterface
{

    /**
     * @var \Church\Validator\Constraints\LoginValidatorInterface
     */
    protected $validator;

    public function __construct(LoginValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array()) : Login
    {
        if (array_key_exists('value', $data)) {
            if ($this->validator->isEmail($data['value'])) {
                $data['type'] = 'email';
            } elseif ($this->validator->isPhone($data['value'])) {
                $data['type'] = 'phone';
            }
        }

        return new Login($data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $type === Login::class;
    }
}
