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
        if (empty($data['features'])) {
            return new $class();
        }

        return $this->createLocationFromFeature($data['features'][0]);
    }

    /**
     * Create a Location object from a feature.
     *
     * @param array $feature
     */
    protected function createLocationFromFeature(array $feature) : Location
    {

        $place_id = null;
        $ancestors = [];
        foreach ($feature['properties'] as $property => $value) {
            $pieces = explode('_', $property);

            if (end($pieces) !== 'gid') {
                continue;
            }

            $pieces = explode(':', $value);
            if ($pieces[0] !== 'whosonfirst') {
                continue;
            }

            $ancestors[] = [
                'ancestor' => [
                    'id' => (int) end($pieces),
                ],
            ];
        }

        $ancestors = array_reverse($ancestors);
        $place = array_shift($ancestors)['ancestor'];
        $place['parent'] = !empty($ancestors) ? reset($ancestors)['ancestor'] : [];
        $place['ancestor'] = $ancestors;

        return new Location([
            'id' => $feature['properties']['gid'] ?? null,
            'longitude' => $feature['geometry']['coordinates'][0] ?? null,
            'latitude' => $feature['geometry']['coordinates'][1] ?? null,
            'place' => $place,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (isset($data['type']) && $data['type'] === 'FeatureCollection' && isset($data['features'])) {
            if ($type === Location::class || is_subclass_of($type, Location::class)) {
                return true;
            }
        }

        return false;
    }
}
