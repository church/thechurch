<?php

namespace Church\Tests\Utils;

use Church\Utils\Slug;

class SlugTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Slug
     */
    protected $slug;

    protected function setUp()
    {
        $this->slug = new Slug();
    }

    /**
     * @dataProvider slugs
     *
     * @param string $text
     * @param string $slug
     */
    public function testSlug(string $text, string $slug) : void
    {
        $created = $this->slug->create($text);

        $this->assertEquals($slug, $created);
    }

    /**
     * Data provider for slug test.
     */
    public function slugs() : array
    {
        return [
            [
                'Orlando',
                'orlando',
            ],
            [
                'Orlando-',
                'orlando',
            ],
            [
                'Saint Petersburg',
                'saint-petersburg',
            ],
            [
                'Saint  Petersburg',
                'saint-petersburg'
            ],
            [
                'St. Petersburg',
                'st-petersburg',
            ],
            [
                'Orléans',
                'orl%C3%A9ans',
            ],
            [
                'Āhualoa',
                '%C4%81hualoa',
            ],
            [
                'Hōnaunau-Napoʻopoʻo',
                'h%C5%8Dnaunau-napo%CA%BBopo%CA%BBo',
            ],
            [
                'Béal Feirste',
                'b%C3%A9al-feirste',
            ],
            [
                'Llandygái',
                'llandyg%C3%A1i',
            ],
            [
                'Caersŵs',
                'caers%C5%B5s',
            ],
            [
                'Aberdâr',
                'aberd%C3%A2r',
            ],
            [
                'Pentredŵr',
                'pentred%C5%B5r',
            ],
            [
                'Llannerch-y-môr',
                'llannerch-y-m%C3%B4r',
            ],
            [
                '香港',
                '%E9%A6%99%E6%B8%AF',
            ],
            [
                '東京',
                '%E6%9D%B1%E4%BA%AC',
            ],
            [
                'Sydney (C)',
                'sydney-c',
            ],
        ];
    }
}
