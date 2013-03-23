<?php

namespace Church\MakeItHappenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function donateAction()
    {
        
        $params = array();
        
        return $this->render('ChurchMakeItHappenBundle:Default:donate.html.twig', $params);
    }
}
