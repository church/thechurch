<?php

namespace Church\Tests\EventListener;

use Church\EventListener\CsrfToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
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
        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $listener = new CsrfToken($tokenManager);

        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

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

        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->setMethod('POST');
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->expectException(BadRequestHttpException::class);
        $result = $listener->onKernelRequest($event);
    }

    /**
     * onKernelRequest event test
     */
    public function testOnKernelRequestInvalidTokenFailure()
    {
        $token = new Token('api', '12345');
        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->expects($this->once())
            ->method('isTokenValid')
            ->willReturn(false);

        $listener = new CsrfToken($tokenManager);

        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->setMethod('POST');
        $request->headers->set('X-CSRF-Token', $token->getId());
        $event = new GetResponseEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->expectException(BadRequestHttpException::class);
        $result = $listener->onKernelRequest($event);
    }
}
