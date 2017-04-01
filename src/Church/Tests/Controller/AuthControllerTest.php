<?php

namespace Church\Tests\Controller;

use Church\Controller\AuthController;
use Church\Entity\User\Email;
use Church\Entity\User\Login;
use Church\Entity\User\User;
use Church\Entity\User\Verify\EmailVerify;
use Church\Entity\User\Verify\VerifyInterface;
use Church\Utils\User\VerificationManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;

class AuthControllerTest extends ControllerTest
{
    public function testLoginAction()
    {
        $denormalizer = $this->getDenormalizer();
        $doctrine = $this->getDoctrine();
        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $jwtManager = $this->createMock(JWTManagerInterface::class);

        $controller = new AuthController(
            $denormalizer,
            $doctrine,
            $verificationManager,
            $jwtManager
        );

        $login = $this->getMockBuilder(Login::class)
            ->disableOriginalConstructor()
            ->getMock();

        $input = [];
        $denormalizer->expects($this->once())
            ->method('denormalize')
            ->with($input, Login::class)
            ->willReturn($login);

        $response = $controller->loginAction($input);

        $this->assertInstanceOf(VerifyInterface::class, $response);
    }

    public function testLoginEmailAction()
    {
        $denormalizer = $this->getDenormalizer();
        $doctrine = $this->getDoctrine();
        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $jwtManager = $this->createMock(JWTManagerInterface::class);

        $controller = new AuthController(
            $denormalizer,
            $doctrine,
            $verificationManager,
            $jwtManager
        );

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

        $input = [];
        $denormalizer->expects($this->once())
            ->method('denormalize')
            ->with($input, EmailVerify::class)
            ->willReturn($verify);

        $response = $controller->loginEmailAction($input);

        $this->assertArrayHasKey("token", $response);
    }
}
