<?php

namespace Church\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route(service="church.controller_csrf")
 */
class CsrfController extends Controller
{

    /**
     * @var Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $tokenManager;

    public function __construct(
        SerializerInterface $serializer,
        CsrfTokenManagerInterface $tokenManager
    ) {
        parent::__construct($serializer);

        $this->tokenManager = $tokenManager;
    }

    /**
     * @Route("/token/{id}",
     *  defaults= {
     *    "id" = "api",
     *    "_format" = "json"
     *  }
     *)
     */
    public function showAction(string $id, Request $request)
    {
        // @TODO need a serializer for the token.
        return $this->reply($this->tokenManager->getToken($id), $request->getRequestFormat());
    }
}
