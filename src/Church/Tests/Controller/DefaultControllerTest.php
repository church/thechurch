<?php

namespace Church\Tests\Controller;

// use Church\Controller\DefaultController;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends ControllerTest
{

    // /**
    //  * Tests the index action.
    //  */
    // public function testIndexAction()
    // {
    //     $request = new Request();
    //     $request->setRequestFormat(self::FORMAT);
    //     $data = [
    //         'hello' => 'world!',
    //     ];
    //     $response = new Response(json_encode($data));
    //
    //     $serializer = $this->getSerializer();
    //     $serializer->expects($this->once())
    //                ->method('respond')
    //                ->with($data, self::FORMAT)
    //                ->willReturn($response);
    //
    //     $default = new DefaultController($serializer, $this->getDoctrine(), $this->getTokenStorage());
    //     $result = $default->indexAction($request);
    //
    //     $this->assertEquals($response, $result);
    // }
}
