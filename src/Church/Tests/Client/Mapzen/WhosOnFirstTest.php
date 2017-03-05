<?php

namespace Church\Test\Client\Mapzen;

// use Church\Client\Mapzen\WhosOnFirst;
// use Church\Entity\Place\Place;
// use GuzzleHttp\ClientInterface;
// use GuzzleHttp\Exception\ClientException;
// use GuzzleHttp\Message\RequestInterface;
// use GuzzleHttp\Message\MessageInterface;
// use GuzzleHttp\Message\ResponseInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use Church\Tests\Client\ClientTest;

class WhosOnFirstTest extends ClientTest
{

    // /**
    //  * Tests the Get method.
    //  */
    // public function testGet()
    // {
    //     $place = new Place([
    //         'id' => 1234,
    //     ]);
    //
    //     $response = $this->createMock(MessageInterface::class);
    //     $client = $this->createMock(ClientInterface::class);
    //     $client->expects($this->once())
    //            ->method('get')
    //            ->willReturn($response);
    //
    //     $serialzer = $this->createMock(SerializerInterface::class);
    //     $serialzer->expects($this->once())
    //               ->method('deserialize')
    //               ->willReturn($place);
    //
    //     $search = new WhosOnFirst($client, $serialzer);
    //     $response = $search->get($place->getId());
    //
    //     $this->assertInstanceOf(Place::class, $response);
    //     $this->assertEquals($place->getId(), $response->getId());
    // }
    //
    // /**
    //  * Test the first response being a bad response.
    //  */
    // public function testGetLoop()
    // {
    //     $place = new Place([
    //         'id' => 1234,
    //     ]);
    //
    //     $request = $this->createMock(RequestInterface::class);
    //     $badResponse = $this->createMock(ResponseInterface::class);
    //     $badResponse->method('getStatusCode')
    //                 ->willReturn(429);
    //     $exception = new ClientException('Too Many Requests', $request, $badResponse);
    //     $response = $this->createMock(MessageInterface::class);
    //     $client = $this->createMock(ClientInterface::class);
    //     $client->expects($this->exactly(2))
    //            ->method('get')
    //            ->willReturnOnConsecutiveCalls($this->throwException($exception), $this->returnValue($response));
    //
    //     $serialzer = $this->createMock(SerializerInterface::class);
    //     $serialzer->expects($this->once())
    //               ->method('deserialize')
    //               ->willReturn($place);
    //
    //     $search = new WhosOnFirst($client, $serialzer);
    //     $response = $search->get($place->getId());
    //
    //     $this->assertInstanceOf(Place::class, $response);
    //     $this->assertEquals($place->getId(), $response->getId());
    // }
    //
    // /**
    //  * Test a complete failure of some kind.
    //  */
    // public function testGetFailure()
    // {
    //     $place = new Place([
    //         'id' => 1234,
    //     ]);
    //
    //     $request = $this->createMock(RequestInterface::class);
    //     $badResponse = $this->createMock(ResponseInterface::class);
    //     $badResponse->method('getStatusCode')
    //                 ->willReturn(500);
    //     $exception = new ClientException('Server Error', $request, $badResponse);
    //     $client = $this->createMock(ClientInterface::class);
    //     $client->expects($this->once())
    //            ->method('get')
    //            ->willThrowException($exception);
    //
    //     $serialzer = $this->createMock(SerializerInterface::class);
    //     $serialzer->expects($this->never())
    //               ->method('deserialize');
    //
    //     $search = new WhosOnFirst($client, $serialzer);
    //
    //     $this->expectException(ClientException::class);
    //     $response = $search->get($place->getId());
    // }
}
