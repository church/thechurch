<?php

namespace Church\Tests\EventListener;

use Church\EventListener\ExceptionListener;
use Church\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelException()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $listener = new ExceptionListener($serializer);

        $exception = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->exactly(2))
            ->method('getRequestFormat')
            ->willReturn('test');

        $event = $this->getMockBuilder(GetResponseForExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getException')
            ->willReturn($exception);

        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $response = $listener->onKernelException($event);

        $this->assertInstanceOf(Response::class, $response);
    }
}
