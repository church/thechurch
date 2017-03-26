<?php

namespace Church\Tests\Controller;

use Church\Controller\AuthController;
use Church\Entity\User\Login;
use Church\Utils\User\VerificationManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthControllerTest extends ControllerTest
{
    public function testLoginAction()
    {
        $serializer = $this->getSerializer();
        $doctrine = $this->getDoctrine();
        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $jwtManager = $this->createMock(JWTManagerInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);

        $controller = new AuthController(
            $serializer,
            $doctrine,
            $tokenStorage,
            $verificationManager,
            $jwtManager,
            $tokenStorage
        );

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getRequestFormat')
            ->willReturn('json');

        $login = $this->getMockBuilder(Login::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serializer->expects($this->once())
            ->method('request')
            ->with($request, Login::class)
            ->willReturn($login);

        $response = $controller->loginAction($request);

        $this->assertInstanceOf(Response::class, $response);
    }
}
