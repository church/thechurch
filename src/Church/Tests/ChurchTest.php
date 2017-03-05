<?php

namespace Church\Tests;

use Church\Church;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ChurchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Build Test.
     */
    public function testBuild()
    {
        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $church = new Church();
        $result = $church->build($container);

        $this->assertNull($result);
    }
}
