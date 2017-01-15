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
     */
    public function indexAction(Request $request)
    {

        /*
        $auth = $this->get('security.authorization_checker');

        if (!$auth->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->forward('Church:User:login');

        } elseif ($auth->isGranted('ROLE_FAITH')) {
            return $this->forward('Church:Place:nearby');

        } elseif (!$auth->isGranted('ROLE_NAME')) {
            return $this->redirect($this->generateUrl('user_name'));

        } elseif (!$auth->isGranted('ROLE_FAITH')) {
            return $this->redirect($this->generateUrl('user_faith'));
        }
        */

        return $this->reply(['hello' => 'world!'], $request->getRequestFormat());
    }
}
