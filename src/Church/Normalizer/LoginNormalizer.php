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
        $login = new Login();

        if (isset($data['value'])) {
            $login->setValue($data['value']);
        }

        if ($value = $login->getValue()) {
            if ($this->validator->isEmail($value)) {
                $login->setType('email');
            } elseif ($this->validator->isPhone($value)) {
                $login->setType('phone');
            }
        }

        return $login;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null) : bool
    {
        return $type === Login::class;
    }
}
