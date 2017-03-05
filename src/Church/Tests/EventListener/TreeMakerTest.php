<?php

namespace Church\Tests\EventListener;

use Church\Entity\Place\Place;
use Church\Entity\Place\Tree;
use Church\EventListener\TreeMaker;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Query\Expr;

class TreeMakerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Post Persist.
     */
    public function testPostPersist()
    {
        $treeMaker = new TreeMaker();

        $parent = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();

        $place = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository = $this->createMock(ObjectRepository::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('getRepository')
            ->with(Tree::class)
            ->willReturn($repository);

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $args->expects($this->once())
            ->method('getEntity')
            ->willReturn($place);
        $args->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);

        $result = $treeMaker->postPersist($args);

        $this->assertNull($result);
    }

    /**
     * Test Pre Update.
     */
    public function testPreUpdate()
    {
        $treeMaker = new TreeMaker();

        $parent_id = 321;
        $parent = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parent->method('getId')
            ->willReturn($parent_id);

        $id = 123;
        $place = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();
        $place->method('getId')
            ->willReturn($id);
        $place->method('getParent')
            ->willReturn($parent);

        $treeRepository = $this->createMock(ObjectRepository::class);
        $placeRepository = $this->createMock(ObjectRepository::class);
        $placeRepository->method('find')
            ->with($id)
            ->willReturn($place);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('getRepository')
            ->willReturnMap([
                [
                    Tree::class,
                    $treeRepository,
                ],
                [
                    Place::class,
                    $placeRepository,
                ],
            ]);

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $args->expects($this->once())
            ->method('getEntity')
            ->willReturn($place);
        $args->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);

        $result = $treeMaker->preUpdate($args);

        $this->assertNull($result);
    }

    /**
     * Test Pre Remove.
     */
    public function testPreRemove()
    {
        $treeMaker = new TreeMaker();

        $place = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $qb->method('expr')
            ->willReturn($expr);
        $qb->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $args = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
        $args->expects($this->once())
            ->method('getEntity')
            ->willReturn($place);
        $args->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);

        $result = $treeMaker->preRemove($args);

        $this->assertNull($result);
    }
}
