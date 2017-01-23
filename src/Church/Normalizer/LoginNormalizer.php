<?php

namespace Church\Normalizer;

use Church\Entity\User\Login;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class LoginNormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array()) : Login
    {
        $login = new Login();

        if (isset($data['value'])) {
            $login->setValue($data['value']);
        }

        // @TODO set the type based on the validator.

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
