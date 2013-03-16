<?php

namespace DavidBarratt\MakeItHappenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DavidBarrattMakeItHappenBundle:Default:index.html.twig', array('name' => $name));
    }
}
