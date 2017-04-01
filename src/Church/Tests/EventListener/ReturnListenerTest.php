<?php

namespace Church\Tests\EventListener;

use Church\EventListener\ReturnListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReturnListenerTest extends \PHPUnit_Framework_TestCase
{

    public function testOnKernelView()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $listener = new ReturnListener($serializer, $tokenStorage);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('test');

        $event = $this->createMock(GetResponseForControllerResultEvent::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn([
                'hello' => 'world!',
            ]);

        $response = $listener->onKernelView($event);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testOnKernelException()
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $listener = new ReturnListener($serializer, $tokenStorage);

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
