<?php

namespace Church\Controller;

use Church\Entity\Place\Place;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Place actions.
 *
 * @Route(
 *    "/place",
 *    service="church.controller_place",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class PlaceController extends Controller
{

    /**
     * @Route("/{place}.{_format}")
     * @Method("GET")
     *
     * @param Place $place
     * @param Request $request
     */
    public function showAction(Place $place, Request $request) : Response
    {
        return $this->reply($place, $request->getRequestFormat());
    }
}
