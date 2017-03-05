<?php

namespace Church\Tests\EventListener;

use Church\EventListener\CsrfToken;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\CsrfToken as Token;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * onKernelRequest event test
     */
    public function testOnKernelRequest()
    {

        $token = $this->getMockBuilder(Token::class)
            ->disableOriginalConstructor()
            ->getMock();
        $token->method('getId')
            ->willReturn('api');
        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(true);
        $listener = new CsrfToken($tokenManager);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getMethod')
            ->willReturn('GET');

        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRequest')
            ->willReturn($request);

        $result = $listener->onKernelRequest($event);
        $this->assertNull($result);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getMethod')
            ->willReturn('POST');
        $request->headers = $this->createMock(ParameterBagInterface::class);
        $request->headers->method('has')
            ->with('X-CSRF-Token')
            ->willReturn(true);
        $request->headers->method('get')
            ->with('X-CSRF-Token')
            ->willReturn('12345');

        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRequest')
            ->willReturn($request);

        $result = $listener->onKernelRequest($event);
        $this->assertNull($result);
    }

    /**
     * onKernelRequest event test
     */
    public function testOnKernelRequestNoHeaderFailure()
    {
        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $listener = new CsrfToken($tokenManager);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getMethod')
            ->willReturn('POST');
        $request->headers = $this->createMock(ParameterBagInterface::class);
        $request->headers->method('has')
            ->with('X-CSRF-Token')
            ->willReturn(false);

        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRequest')
            ->willReturn($request);

        $this->expectException(BadRequestHttpException::class);
        $result = $listener->onKernelRequest($event);
    }

    /**
     * onKernelRequest event test
     */
    public function testOnKernelRequestInvalidTokenFailure()
    {

        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $listener = new CsrfToken($tokenManager);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getMethod')
            ->willReturn('POST');
        $request->headers = $this->createMock(ParameterBagInterface::class);
        $request->headers->method('has')
            ->with('X-CSRF-Token')
            ->willReturn(true);
        $request->headers->method('get')
            ->with('X-CSRF-Token')
            ->willReturn('12345');

        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRequest')
            ->willReturn($request);

        $this->expectException(BadRequestHttpException::class);
        $result = $listener->onKernelRequest($event);
    }
}
