<?php

namespace Church\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route(service="church.controller_default")
 */
class DefaultController extends Controller
{

    /**
     * @Route("/",
     *  defaults= {
     *    "_format" = "json"
     *  }
     *)
     *
     * @param Request $request
     */
    public function indexAction(Request $request) : Response
    {
        return $this->reply(['hello' => 'world!'], $request->getRequestFormat());
    }
}
