<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {

      $auth = $this->get('security.authorization_checker');
      if ($auth->isGranted('ROLE_FAITH')) {
        return $this->forward('Church:Place:nearby');
      }
      else if (!$auth->isGranted('ROLE_NAME')) {
        return $this->redirect($this->generateUrl('user_name'));
      }
      else if (!$auth->isGranted('ROLE_FAITH')) {
        return $this->redirect($this->generateUrl('user_faith'));
      }

      return $this->forward('Church:User:login');

    }

}
