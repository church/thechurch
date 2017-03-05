<?php

namespace Church\Tests\Controller;

use Church\Controller\MeController;
use Church\Entity\User\User;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MeControllerTest extends ControllerTest
{

    /**
     * Tests the show action.
     */
    public function testshowAction()
    {
        $request = new Request();
        $request->setRequestFormat(self::FORMAT);

        $data = [
            'id' => '12345'
        ];
        $user = new User($data);
        $response = new Response(json_encode($data));

        $token = $this->getToken();
        $token->expects($this->once())
              ->method('getUser')
              ->willReturn($user);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->once())
                     ->method('getToken')
                     ->willReturn($token);

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
                   ->method('respond')
                   ->with($user, self::FORMAT, ['me'])
                   ->willReturn($response);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $me = new MeController(
            $serializer,
            $this->getDoctrine(),
            $tokenStorage,
            $verificationManager,
            $csrfTokenManager,
            $placeFinder
        );
        $result = $me->showAction($request);

        $this->assertEquals($response, $result);
    }

    /**
     * Tests the show action no user failure.
     */
    public function testshowActionNoUserFailure()
    {
        $request = new Request();
        $request->setRequestFormat(self::FORMAT);

        $data = [
            'id' => '12345'
        ];
        $user = new User($data);

        $token = $this->getToken();
        $token->expects($this->once())
              ->method('getUser')
              ->willReturn(null);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->once())
                     ->method('getToken')
                     ->willReturn($token);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $me = new MeController(
            $this->getSerializer(),
            $this->getDoctrine(),
            $tokenStorage,
            $verificationManager,
            $csrfTokenManager,
            $placeFinder
        );

        $this->expectException(\Exception::class);
        $result = $me->showAction($request);
    }

    /**
     * Tests the show action no token failure.
     */
    public function testshowActionNoTokenFailure()
    {
        $request = new Request();
        $request->setRequestFormat(self::FORMAT);

        $data = [
            'id' => '12345'
        ];
        $user = new User($data);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->once())
                     ->method('getToken')
                     ->willReturn(null);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $me = new MeController(
            $this->getSerializer(),
            $this->getDoctrine(),
            $tokenStorage,
            $verificationManager,
            $csrfTokenManager,
            $placeFinder
        );

        $this->expectException(\Exception::class);
        $result = $me->showAction($request);
    }

    /**
     * Update user test.
     */
    public function testUpdateAction()
    {
        $request = new Request();
        $request->setRequestFormat(self::FORMAT);

        $data = [
            'id' => '427bb4c4-4481-41b2-88f4-ce1980598208',
            'name' => 'test',
        ];
        $user = new User($data);

        $updated = $data;
        $updated['name'] = 'test2';
        $new = new User($updated);
        $response = new Response(json_encode($updated));

        $token = $this->getToken();
        $token->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->getTokenStorage();
        $tokenStorage->expects($this->exactly(3))
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
        $repository->expects($this->once())
            ->method('find')
            ->with($user->getId())
            ->willReturn($user);

        $em = $this->getEntityManager();
        $em->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($repository);

        $doctrine = $this->getDoctrine();
        $doctrine->expects($this->once())
                 ->method('getEntityManager')
                 ->willReturn($em);

        $verificationManager = $this->createMock(VerificationManagerInterface::class);
        $csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $placeFinder = $this->createMock(PlaceFinderInterface::class);

        $me = new MeController(
            $serializer,
            $doctrine,
            $tokenStorage,
            $verificationManager,
            $csrfTokenManager,
            $placeFinder
        );
        $result = $me->updateAction($request);

        $this->assertEquals($response, $result);
    }
}
