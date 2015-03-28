<?php

namespace Church\Tests\Utils;

use Patchwork\Utf8;

use Church\Utils\Slug;

class SlugTest extends \PHPUnit_Framework_TestCase
{

    protected $slug;

    protected function setUp()
    {
        $this->slug = new Slug(new Utf8());
    }

    /**
     * @dataProvider slugs
     */
    public function testSlug($text, $slug)
    {
        $created = $this->slug->create($text);

        $this->assertEquals($slug, $created);
    }

    public function slugs()
    {
        return array(
            array(
                'Orlando',
                'orlando',
            ),
            array(
                'Saint Petersburg',
                'saint-petersburg'
            ),
            array(
                'St. Petersburg',
                'st-petersburg',
            ),
            array(
                'Orléans',
                'orl%E3%A9ans',
            ),
            array(
                'Āhualoa',
                '%E4%80hualoa',
            ),
            array(
                'Hōnaunau-Napoʻopoʻo',
                'h%E5%8Dnaunau-napo%EA%BBopo%EA%BBo',
            ),
            array(
                'Béal Feirste',
                'b%E3%A9al-feirste',
            ),
            array(
                'Llandygái',
                'llandyg%E3%A1i',
            ),
            array(
                'Caersŵs',
                'caers%E5%B5s',
            ),
            array(
                'Aberdâr',
                'aberd%E3%A2r',
            ),
            array(
                'Pentredŵr',
                'pentred%E5%B5r',
            ),
            array(
                'Llannerch-y-môr',
                'llannerch-y-m%E3%B4r',
            ),
            array(
                '香港',
                '%E9%A6%99%E6%B8%AF',
            ),
            array(
                '東京',
                '%E6%9D%B1%E4%BA%AC',
            ),
        );
    }
}
