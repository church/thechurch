<?php

namespace Church\TeaserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ChurchTeaserBundle:Default:index.html.twig', array('name' => $name));
    }
}
