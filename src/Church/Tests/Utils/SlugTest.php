<?php

namespace Church\Tests\Utils;

use Church\Utils\Slug;

class SlugTest extends \PHPUnit_Framework_TestCase
{

    protected $slug;

    protected function setUp()
    {
        $this->slug = new Slug();
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
                'orl%C3%A9ans',
            ),
            array(
                'Āhualoa',
                '%C4%81hualoa',
            ),
            array(
                'Hōnaunau-Napoʻopoʻo',
                'h%C5%8Dnaunau-napo%CA%BBopo%CA%BBo',
            ),
            array(
                'Béal Feirste',
                'b%C3%A9al-feirste',
            ),
            array(
                'Llandygái',
                'llandyg%C3%A1i',
            ),
            array(
                'Caersŵs',
                'caers%C5%B5s',
            ),
            array(
                'Aberdâr',
                'aberd%C3%A2r',
            ),
            array(
                'Pentredŵr',
                'pentred%C5%B5r',
            ),
            array(
                'Llannerch-y-môr',
                'llannerch-y-m%C3%B4r',
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
