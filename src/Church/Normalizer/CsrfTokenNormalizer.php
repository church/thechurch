<?php

namespace Church\Normalizer;

use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * A CSRF token normalizer.
 */
class CsrfTokenNormalizer implements NormalizerInterface
{
   /**
    * {@inheritdoc}
    *
    * Convert the object to a string.
    */
    public function normalize($object, $format = null, array $context = array()) : array
    {
        return [
          'id' => $object->getId(),
          'value' => $object->getValue(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof CsrfToken;
    }
}
