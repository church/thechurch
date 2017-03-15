<?php

namespace Church\Tests\Controller;

use Church\Controller\UserController;
use Church\Entity\User\User;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $user->expects($this->once())
            ->method('isEqualTo')
            ->with($user)
            ->willReturn(true);

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getToken();
        $token->expects($this->exactly(2))
              ->method('getUser')
              ->willReturn($user);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->exactly(2))
                     ->method('getToken')
                     ->willReturn($token);

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
                   ->method('respond')
                   ->with($user, self::FORMAT, ['me'])
                   ->willReturn($response);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $serializer,
            $this->getDoctrine(),
            $tokenStorage,
            $verificationManager,
            $placeFinder
        );
        $result = $controller->showAction($user, $request);

        $this->assertEquals($response, $result);
    }

    /**
     * Tests the show action no user failure.
     */
    public function testshowActionNoUserFailure()
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

        $token = $this->getToken();
        $token->expects($this->never())
              ->method('getUser');

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->never())
                     ->method('getToken');

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $this->getSerializer(),
            $this->getDoctrine(),
            $tokenStorage,
            $verificationManager,
            $placeFinder
        );

        $this->expectException(\Exception::class);
        $result = $controller->showAction($user, $request);
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

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->never())
                     ->method('getToken');

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $controller = new UserController(
            $this->getSerializer(),
            $this->getDoctrine(),
            $tokenStorage,
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
        $user->expects($this->exactly(3))
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

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $token = $this->getToken();
        $token->expects($this->exactly(5))
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->exactly(5))
            ->method('getToken')
            ->willReturn($token);

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
            ->method('request')
            ->with($request, $user, ['me'])
            ->willReturn($new);

        $serializer->expects($this->once())
            ->method('respond')
            ->with($new, self::FORMAT, ['me'])
            ->willReturn($response);

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
            $serializer,
            $doctrine,
            $tokenStorage,
            $verificationManager,
            $placeFinder
        );
        $result = $controller->updateAction($user, $request);

        $this->assertEquals($response, $result);
    }
}
