<?php

namespace Church\Tests\Utils;

use Church\Entity\Location;
use Church\Client\Mapzen\SearchInterface;
use Church\Client\Mapzen\WhosOnFirstInterface;
use Church\Entity\Place\Place;
use Church\Entity\Place\Tree;
use Church\Utils\PlaceFinder;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Array Utilties Test.
 */
class PlaceFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the find method.
     */
    public function testFind()
    {

        $place_id = 321;
        $place = $this->getMockBuilder(Place::class)
            ->disableOriginalConstructor()
            ->getMock();
        $place->method('getId')
            ->willReturn($place_id);
        $place->method('getName')
            ->willReturn('Orlando');

        $id = '123';
        $location = $this->getMockBuilder(Location::class)
            ->disableOriginalConstructor()
            ->getMock();
        $location->method('getId')
            ->willReturn($id);
        $location->method('getPlace')
            ->willReturn($place);

        $locationRepository = $this->createMock(ObjectRepository::class);

        $placeRepository = $this->createMock(ObjectRepository::class);
        $placeRepository->method('find')
            ->with($place_id)
            ->willReturn($place);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')
            ->willReturnMap([
                [
                    Location::class,
                    $locationRepository,
                ],
                [
                    Place::class,
                    $placeRepository,
                ],
            ]);

        $doctrine = $this->createMock(RegistryInterface::class);
        $doctrine->method('getEntityManager')
            ->willReturn($em);

        $search = $this->createMock(SearchInterface::class);
        $search->method('get')
            ->with($id)
            ->willReturn($location);

        $whosonfirst = $this->createMock(WhosOnFirstInterface::class);
        $whosonfirst->method('get')
            ->with($place_id)
            ->willReturn($place);

        $placeFinder = new PlaceFinder($doctrine, $search, $whosonfirst);

        $result = $placeFinder->find($location);
        $this->assertInstanceOf(Location::class, $result);

        $this->resetCount();

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $exception = $this->getMockBuilder(ClientException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $exception->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $whosonfirst
            ->method('get')
            ->with($place_id)
            ->willReturnOnConsecutiveCalls(
                $this->throwException($exception),
                $this->returnValue($place)
            );

        $tree = $this->getMockBuilder(Tree::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tree->expects($this->once())
            ->method('getAncestor')
            ->willReturn($place);

        $ancestor = $this->createMock(Collection::class);
        $ancestor->expects($this->once())
            ->method('first')
            ->willReturn($tree);

        $place->expects($this->once())
            ->method('getAncestor')
            ->willReturn($ancestor);

        $result = $placeFinder->find($location);
        $this->assertInstanceOf(Location::class, $result);
    }
}
