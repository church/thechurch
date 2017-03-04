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
        $request = new Request();
        $request->setRequestFormat(self::FORMAT);

        $id = 'api';
        $value = '12345';
        $token = new CsrfToken($id, $value);
        $data = [$id, $value];
        $response = new Response(json_encode($data));

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
                   ->method('respond')
                   ->with($token, self::FORMAT)
                   ->willReturn($response);

        $tokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $tokenManager->expects($this->once())
                     ->method('getToken')
                     ->with($id)
                     ->willReturn($token);

        $default = new CsrfController($serializer, $this->getDoctrine(), $this->getTokenStorage(), $tokenManager);
        $result = $default->showAction($request);

        $this->assertEquals($response, $result);
    }
}
