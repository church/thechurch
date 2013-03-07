<?php

namespace Church\PlaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Church\PlaceBundle\Entity\City;

class DefaultController extends Controller
{
    public function cityAction($slug)
    {
    
        $repositry = $this->getDoctrine()->getRepository('ChurchPlaceBundle:City');
        
        $city = $repositry->findOneBySlug($slug);
        
        if (!$city) {
           throw $this->createNotFoundException("The City doesn't exist yet...");
        }

        return $this->render('ChurchPlaceBundle:Default:city.html.twig', array('city' => $city));
        
    }
}
