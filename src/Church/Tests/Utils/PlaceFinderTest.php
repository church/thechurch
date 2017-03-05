<?php

namespace Church\Tests\Utils;

use Church\Entity\Location;
use Church\Client\Mapzen\SearchInterface;
use Church\Client\Mapzen\WhosOnFirstInterface;
use Church\Entity\Place\Place;
use Church\Utils\PlaceFinder;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        $location = new Location([
            'id' => '123',
            'place' => [
                'id' => 321,
                'name' => 'Orlando',
                'created' => new \DateTime(),
            ],
        ]);

        $locationRepository = $this->createMock(ObjectRepository::class);

        $placeRepository = $this->createMock(ObjectRepository::class);
        $placeRepository->expects($this->exactly(2))
            ->method('find')
            ->with($location->getPlace()->getId())
            ->willReturn($location->getPlace());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(2))
            ->method('getRepository')
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
        $doctrine->expects($this->exactly(2))
            ->method('getEntityManager')
            ->willReturn($em);

        $search = $this->createMock(SearchInterface::class);
        $search->expects($this->once())
            ->method('get')
            ->with($location->getId())
            ->willReturn($location);

        $whosonfirst = $this->createMock(WhosOnFirstInterface::class);
        $whosonfirst->expects($this->once())
            ->method('get')
            ->with($location->getPlace()->getId())
            ->willReturn($location->getPlace());

        $placeFinder = new PlaceFinder($doctrine, $search, $whosonfirst);

        $result = $placeFinder->find($location);

        $this->assertEquals($location, $result);
    }
}
