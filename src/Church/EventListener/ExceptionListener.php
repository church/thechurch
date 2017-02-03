<?php

namespace Church\EventListener;

use Church\Response\SerializerResponseTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener
{

    use SerializerResponseTrait;

    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event) : Response
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'error' => $exception->getMessage(),
            ];
        } else {
            $data = [
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
        }

        // Override the default request format.
        if ($request->getRequestFormat() === 'html') {
            $request->setRequestFormat('json');
        }

        if ($exception instanceof HttpExceptionInterface) {
            $response = $this->reply(
                $data,
                $request->getRequestFormat(),
                $exception->getStatusCode(),
                $exception->getHeaders()
            );
        } else {
            $response = $this->reply($data, $request->getRequestFormat(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send the modified response object to the event
        $event->setResponse($response);

        return $response;
    }
}
