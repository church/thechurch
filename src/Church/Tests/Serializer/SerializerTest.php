<?php

namespace Church\Tests\Serializer;

use Church\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Serializer Test.
 */
class SerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the Request method.
     */
    public function testRequest()
    {
        $data = new \stdClass();
        $data->id = 123;

        $request = new Request([], [], [], [], [], [], json_encode($data));
        $request->setRequestFormat('json');

        $s = $this->createMock(SerializerInterface::class);
        $s->expects($this->once())
            ->method('deserialize')
            ->with($request->getContent(), \stdClass::class, $request->getRequestFormat())
            ->willReturn($data);


        $validator = $this->createMock(ValidatorInterface::class);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $serializer = new Serializer($s, $validator, $tokenStorage, '*');

        $result = $serializer->request($request, \stdClass::class);

        $this->assertEquals($data, $result);
    }
}
