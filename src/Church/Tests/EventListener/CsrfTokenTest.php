<?php

namespace Church\Tests\EventListener;

use Church\EventListener\CsrfToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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
}
