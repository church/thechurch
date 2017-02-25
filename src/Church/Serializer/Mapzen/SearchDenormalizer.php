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
     * @var string[]
     */
    const PLACE_TYPES = [
        'venue',
        'address',
        'building',
        'campus',
        'microhood',
        'neighbourhood',
        'macrohood',
        'locality',
        'metro_area',
        'county',
        'macrocounty',
        'region',
        'macroregion',
        'country',
        'empire',
        'continent',
    ];

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (substr($class, -2) === '[]') {
            $class = substr($class, -2) === '[]' ? substr($type, 0, -2) : $class;
            $locaitons = [];
            foreach ($data['features'] as $feature) {
                $locaitons[] = $this->createLocationFromFeature($feature);
            }

            return $locations;
        }

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
        foreach (self::PLACE_TYPES as $type) {
            if (isset($feature['properties'][$type . '_gid'])) {
                $pieces = explode(':', $feature['properties'][$type . '_gid']);
                if ($pieces[0] !== 'whosonfirst') {
                    continue;
                }
                if (!$place_id) {
                    $place_id = (int) end($pieces);
                    continue;
                }

                $ancestors[] = [
                    'ancestor' => [
                        'id' => (int) end($pieces),
                    ],
                ];
            }
        }

        return new Location([
            'id' => $feature['properties']['gid'] ?? null,
            'longitude' => $feature['geometry']['coordinates'][0] ?? null,
            'latitude' => $feature['geometry']['coordinates'][1] ?? null,
            'place' => [
                'id' => $place_id,
                'parent' => !empty($ancestors) ? reset($ancestors)['ancestor'] : [],
                'ancestor' => $ancestors,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (isset($data['type']) && $data['type'] === 'FeatureCollection' && isset($data['features'])) {
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
