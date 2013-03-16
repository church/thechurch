<?php

namespace Church\MakeItHappenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ChurchMakeItHappenBundle:Default:index.html.twig', array('name' => $name));
    }
}
