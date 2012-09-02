<?php

namespace Church\TeaserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($slug)
    {
        return $this->render('ChurchTeaserBundle:Default:index.html.twig', array('slug' => $slug));
    }
}
