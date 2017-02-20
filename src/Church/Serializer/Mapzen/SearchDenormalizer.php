<?php

namespace Church\Serializer\Mapzen;

use Church\Entity\Location;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes the Search Response.
 */
class SearchDenormalizer implements DenormalizerInterface
{

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (substr($class, -2) === '[]') {
            $class = substr($class, -2) === '[]' ? substr($type, 0, -2) : $class;
            $locaitons = [];
            foreach ($data['geocoding']['features'] as $feature) {
                $locaitons[] = $this->createLocationFromFeature($feature);
            }

            return $locations;
        }

        if (empty($data['geocoding']['features'])) {
            return new Location();
        }

        return $this->createLocationFromFeature($data['geocoding']['features'][0]);
    }

    /**
     * Create a Location object from a feature.
     *
     * @param array $feature
     */
    protected function createLocationFromFeature(array $feature) : Location
    {
        return new Location([
            'id' => $feature['gid'] ?? null,
            'latitude' => $feature['cordinates'][0] ?? null,
            'longitude' => $feature['cordinates'][1] ?? null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (isset($data['geocoding']) && isset($data['geocoding']['features'])) {
            if ($type === Location::class) {
                return true;
            }
            if (substr($type, -2) === '[]' && substr($type, 0, -2) === Location::class) {
                return true;
            }
        }

        return false;
    }
}
