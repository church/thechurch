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
    public function normalize($object, $format = null, array $context = array()) : string
    {
        return (string) $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null) : bool
    {
        return $data instanceof CsrfToken;
    }
}
