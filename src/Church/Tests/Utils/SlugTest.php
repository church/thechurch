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
                'orleans',
            ),
            array(
                'Āhualoa',
                'ahualoa',
            ),
            array(
                'Hōnaunau-Napoʻopoʻo',
                'honaunau-napoopoo',
            ),
            array(
                'Béal Feirste',
                'beal-feirste',
            ),
            array(
                'Llandygái',
                'llandygai',
            ),
            array(
                'Caersŵs',
                'caersws',
            ),
            array(
                'Aberdâr',
                'aberdar',
            ),
            array(
                'Pentredŵr',
                'pentredwr',
            ),
            array(
                'Llannerch-y-môr',
                'llannerch-y-mor',
            ),
        );
    }
}
