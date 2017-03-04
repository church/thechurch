<?php

namespace Church\Test\Client\Mapzen;

use Church\Client\Mapzen\Search;
use Church\Entity\Location;
use Church\Tests\Client\ClientTest;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\MessageInterface;
use GuzzleHttp\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SearchTest extends ClientTest
{

    /**
     * Tests the Get method.
     */
    public function testGet()
    {
        $location = new Location([
            'id' => '1234',
        ]);

        $response = $this->createMock(MessageInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('get')
               ->willReturn($response);

        $serialzer = $this->createMock(SerializerInterface::class);
        $serialzer->expects($this->once())
                  ->method('deserialize')
                  ->willReturn($location);

        $search = new Search($client, $serialzer);
        $response = $search->get($location->getId());

        $this->assertInstanceOf(Location::class, $response);
        $this->assertEquals($location->getId(), $response->getId());
    }

    /**
     * Test the first response being a bad response.
     */
    public function testGetLoop()
    {
        $location = new Location([
            'id' => '1234',
        ]);

        $request = $this->createMock(RequestInterface::class);
        $badResponse = $this->createMock(ResponseInterface::class);
        $badResponse->method('getStatusCode')
                    ->willReturn(429);
        $exception = new ClientException('Too Many Requests', $request, $badResponse);
        $response = $this->createMock(MessageInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->exactly(2))
               ->method('get')
               ->willReturnOnConsecutiveCalls($this->throwException($exception), $this->returnValue($response));

        $serialzer = $this->createMock(SerializerInterface::class);
        $serialzer->expects($this->once())
                  ->method('deserialize')
                  ->willReturn($location);

        $search = new Search($client, $serialzer);
        $response = $search->get($location->getId());

        $this->assertInstanceOf(Location::class, $response);
        $this->assertEquals($location->getId(), $response->getId());
    }

    /**
     * Test a complete failure of some kind.
     */
    public function testGetFailure()
    {
        $location = new Location([
            'id' => '1234',
        ]);

        $request = $this->createMock(RequestInterface::class);
        $badResponse = $this->createMock(ResponseInterface::class);
        $badResponse->method('getStatusCode')
                    ->willReturn(500);
        $exception = new ClientException('Server Error', $request, $badResponse);
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
               ->method('get')
               ->willThrowException($exception);

        $serialzer = $this->createMock(SerializerInterface::class);
        $serialzer->expects($this->never())
                  ->method('deserialize');

        $search = new Search($client, $serialzer);

        $this->expectException(ClientException::class);
        $response = $search->get($location->getId());
    }
}
