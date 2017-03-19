<?php

namespace Church\Tests\Serializer\Mapzen;

use Church\Entity\Location;
use Church\Serializer\Mapzen\SearchDenormalizer;

class SearchDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    public function testDenormalize()
    {
        $gid = "whosonfirst:neighbourhood:874397665";
        $data = [
            'features' => [
                [
                    "properties" => [
                            "gid" => $gid,
                    ],
                ],
            ],
        ];
        $normalizer = new SearchDenormalizer();
        $location = $normalizer->denormalize($data, Location::class, 'test');

        $this->assertEquals($gid, $location->getId());
    }
}
