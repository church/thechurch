<?php

namespace Church\PlaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Church\PlaceBundle\Entity\City;

class DefaultController extends Controller
{
    public function cityAction($slug)
    {
    
        $repositry = $this->getDoctrine()->getRepository('ChurchPlaceBundle:City');
        
        $city = $repositry->findCityBySlug($slug);
                
        if (!$city) {
           throw $this->createNotFoundException("The City doesn't exist yet...");
        }
        
        $city = $city->getPlace();
        
        $repositry = $this->getDoctrine()->getRepository('ChurchPlaceBundle:Place');
        
        $state = $repositry->findState($city->getID());
        
        $country = $repositry->findCountry($city->getID());
        
        $variables = array(
          'city' => $city,
          'state' => $state,
          'country' => $country,
        );

        return $this->render('ChurchPlaceBundle:Default:city.html.twig', $variables);
        
    }
}
