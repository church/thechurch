<?php

namespace Church\Tests\Controller;

use Church\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends ControllerTest
{

    /**
     * Tests the index action.
     */
    public function testIndexAction()
    {
        $data = [
            'hello' => 'world!',
        ];
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('getRequestFormat')
            ->willReturn(self::FORMAT);

        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serializer = $this->getSerializer();
        $serializer->expects($this->once())
                   ->method('respond')
                   ->with($data, self::FORMAT)
                   ->willReturn($response);

        $default = new DefaultController($serializer, $this->getDoctrine(), $this->getTokenStorage());
        $result = $default->indexAction($request);

        $this->assertEquals($response, $result);
    }
}
