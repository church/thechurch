<?php

namespace Church\Tests\Controller;

use Church\Controller\UserController;
use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends ControllerTest
{

    /**
     * Tests the show action.
     */
    public function testshowAction()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getRequestFormat')
            ->willReturn(self::FORMAT);

        $data = [
            'id' => '427bb4c4-4481-41b2-88f4-ce1980598208'
        ];
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getId')
            ->willReturn($data['id']);
        $user->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $denormalizer = $this->getDenormalizer();

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $denormalizer,
            $this->getDoctrine(),
            $verificationManager,
            $placeFinder
        );

        $result = $controller->showAction($user, $request, $user);

        $this->assertEquals($user, $result);
    }

    /**
     * Tests the show action no user failure.
     */
    public function testshowActionNoUserFailure()
    {
        $data = [
            'id' => '427bb4c4-4481-41b2-88f4-ce1980598208'
        ];
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getId')
            ->willReturn($data['id']);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $this->getDenormalizer(),
            $this->getDoctrine(),
            $verificationManager,
            $placeFinder
        );

        $this->expectException(\Exception::class);
        $result = $controller->showAction($user);
    }

    /**
     * Tests the show action no token failure.
     */
    public function testshowActionNoTokenFailure()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getRequestFormat')
            ->willReturn(self::FORMAT);

        $data = [
            'id' => '427bb4c4-4481-41b2-88f4-ce1980598208'
        ];
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getId')
            ->willReturn($data['id']);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $this->getDenormalizer(),
            $this->getDoctrine(),
            $verificationManager,
            $placeFinder
        );

        $this->expectException(\Exception::class);
        $result = $controller->showAction($user, $request);
    }

    /**
     * Update user test.
     */
    public function testUpdateAction()
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getRequestFormat')
            ->willReturn(self::FORMAT);

        $data = [
            'id' => '427bb4c4-4481-41b2-88f4-ce1980598208',
            'username' => 'test',
        ];
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->method('getId')
            ->willReturn($data['id']);
        $user->expects($this->once())
            ->method('isEqualTo')
            ->with($user)
            ->willReturn(true);

        $new = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $new->method('getId')
            ->willReturn($data['id']);
        $new->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $input = [];
        $denormalizer = $this->getDenormalizer();
        $denormalizer->expects($this->once())
            ->method('denormalize')
            ->with($input, $user)
            ->willReturn($new);

        $repository = $this->getRepository();
        $repository->expects($this->never())
            ->method('find');

        $em = $this->getEntityManager();
        $em->expects($this->never())
            ->method('getRepository');

        $doctrine = $this->getDoctrine();
        $doctrine->expects($this->once())
                 ->method('getEntityManager')
                 ->willReturn($em);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $denormalizer,
            $doctrine,
            $verificationManager,
            $placeFinder
        );
        $result = $controller->updateAction($user, $user, $input);

        $this->assertEquals($new, $result);
    }

    public function testVerifyEmailAction()
    {
        $denormalizer = $this->getDenormalizer();

        $collection = $this->createMock(Collection::class);

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $user->expects($this->once())
            ->method('isEqualTo')
            ->with($user)
            ->willReturn(true);
        $user->expects($this->once())
            ->method('getEmails')
            ->willReturn($collection);
        $user->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $email = $this->getMockBuilder(Email::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token = 'abc';
        $verify = $this->getMockBuilder(EmailVerify::class)
            ->disableOriginalConstructor()
            ->getMock();
        $verify->expects($this->once())
            ->method('getToken')
            ->willReturn($token);
        $verify->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $verify->expects($this->once())
            ->method('isEqualTo')
            ->with($verify)
            ->willReturn(true);
        $verify->expects($this->once())
            ->method('isFresh')
            ->willReturn(true);

        $repository = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('__call')
            ->with('findOneByToken', [$token])
            ->willReturn($verify);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $doctrine = $this->getDoctrine();
        $doctrine->expects($this->once())
            ->method('getRepository')
            ->with(EmailVerify::class)
            ->willReturn($repository);
        $doctrine->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($em);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $denormalizer,
            $doctrine,
            $verificationManager,
            $placeFinder
        );

        $input = [];
        $denormalizer->expects($this->once())
            ->method('denormalize')
            ->with($input, EmailVerify::class)
            ->willReturn($verify);

        $response = $controller->verifyEmailAction($user, $user, $input);

        $this->assertInstanceOf(Collection::class, $response);
    }
}
