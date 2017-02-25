<?php

namespace Church\Serializer\Mapzen;

use Church\Entity\Place\Place;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes the Search Response.
 */
class WhosOnFirstDenormalizer implements DenormalizerInterface
{

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $parent = null;
        if (!empty($data['properties']['wof:parent_id']) && $data['properties']['wof:parent_id'] != '-1') {
            $parent = [
                'id' => (int) $data['properties']['wof:parent_id'],
            ];
        }
        return new $class([
            'id' => $data['id'] ?? null,
            'parent' => $parent,
            'name' => $this->getName($data),
        ]);
    }

    /**
     * Get the names from the data.
     *
     * @param array $data
     */
    protected function getName(array $data) : string
    {
        $langs = [];
        if (!empty($data['properties']['wof:lang_x_official'])) {
            $langs = $data['properties']['wof:lang_x_official'];
        } elseif (!empty($data['properties']['wof:lang'])) {
            $langs = $data['properties']['wof:lang'];
        }
        foreach ($langs as $lang) {
            if (!empty($data['properties']['name:' . $lang . '_x_preferred'])) {
                return $data['properties']['name:' . $lang . '_x_preferred'][0];
            }
        }

        return $data['properties']['wof:name'] ?? '';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (isset($data['type']) && $data['type'] === 'Feature' && isset($data['properties'])) {
            if ($type === Place::class) {
                return true;
            }
        }

        return false;
    }
}
