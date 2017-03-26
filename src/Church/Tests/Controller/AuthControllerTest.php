<?php

namespace Church\Tests\Controller;

use Church\Controller\AuthController;
use Church\Entity\User\Email;
use Church\Entity\User\Login;
use Church\Entity\User\User;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\User\VerificationManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
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
            ->willReturn('test');

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

    public function testLoginEmailAction()
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
            ->willReturn('test');

        $token = 'abc';

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();
        $email->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $verify = $this->getMockBuilder(EmailVerify::class)
            ->disableOriginalConstructor()
            ->getMock();
        $verify->expects($this->once())
            ->method('getToken')
            ->willReturn($token);
        $verify->expects($this->once())
            ->method('isFresh')
            ->willReturn(true);
        $verify->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $verify->expects($this->once())
            ->method('isEqualTo')
            ->with($verify)
            ->willReturn(true);

        $serializer->expects($this->once())
            ->method('request')
            ->with($request, EmailVerify::class)
            ->willReturn($verify);

        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('__call')
            ->with('findOneByToken', [$token])
            ->willReturn($verify);

        $em = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doctrine->expects($this->once())
            ->method('getRepository')
            ->with(EmailVerify::class)
            ->willReturn($repository);
        $doctrine->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);

        $response = $controller->loginEmailAction($request);

        $this->assertInstanceOf(Response::class, $response);
    }
}
