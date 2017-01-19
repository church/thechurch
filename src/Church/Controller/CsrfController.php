<?php

namespace Church\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route(service="church.controller_csrf")
 */
class CsrfController extends Controller
{

    /**
     * @Route("/token.{_format}",
   *  defaults= {
     *    "_format" = "json"
     *  }
     *)
     */
    public function showAction(Request $request)
    {
        return $this->reply($this->csrfTokenManager->getToken(self::CSRF_TOKEN_ID), $request->getRequestFormat());
    }
}
