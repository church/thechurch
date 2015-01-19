<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{

    /**
     * @Route("/{slug}", name="teaser", defaults={"slug" = "none"})
     */
    public function indexAction($slug)
    {
        return $this->render('default/index.html.twig', array('slug' => $slug));
    }

}
