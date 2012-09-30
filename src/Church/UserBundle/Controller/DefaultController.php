<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ChurchUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
