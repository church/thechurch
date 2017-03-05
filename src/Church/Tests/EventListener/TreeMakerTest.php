<?php

namespace Church\Tests\EventListener;

use Church\EventListener\TreeMaker;
use Doctrine\ORM\Event\LifecycleEventArgs;

class TreeMakerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Post Persist.
     */
    public function testPostPersist()
    {
        $treeMaker = new TreeMaker();

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $treeMaker->postPersist($args);

        $this->assertNull($result);
    }

    /**
     * Test Pre Update.
     */
    public function testPreUpdate()
    {
        $treeMaker = new TreeMaker();

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $treeMaker->preUpdate($args);

        $this->assertNull($result);
    }

    /**
     * Test Pre Remove.
     */
    public function testPreRemove()
    {
        $treeMaker = new TreeMaker();

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $treeMaker->preRemove($args);

        $this->assertNull($result);
    }
}
