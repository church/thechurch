<?php

namespace Church\Tests\Controller;

use Church\Controller\CsrfController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfControllerTest extends ControllerTest
{

    /**
     * Tests the show action.
     */
    public function testShowAction()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getRequestFormat')
            ->willReturn(self::FORMAT);

        $data = [
            'id' => 'api',
            'value' => '12345',
        ];
        $token = $this->getMockBuilder(CsrfToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $token->method('getId')
            ->willReturn($data['id']);

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
                   ->method('respond')
                   ->with($token, self::FORMAT)
                   ->willReturn($response);

        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->expects($this->once())
                     ->method('getToken')
                     ->with($token->getId())
                     ->willReturn($token);

        $default = new CsrfController($serializer, $this->getDoctrine(), $this->getTokenStorage(), $tokenManager);
        $result = $default->showAction($request);

        $this->assertEquals($response, $result);
    }
}
