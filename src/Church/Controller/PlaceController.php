<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Church\Entity\City;
use Church\Entity\Place;

class PlaceController extends Controller
{

    /**
     * @Route("/{slug}", name="place_city")
     */
    public function cityAction($slug)
    {

        $repositry = $this->getDoctrine()->getRepository('Church:City');

        $city = $repositry->findCityBySlug($slug);

        if (!$city) {
           throw $this->createNotFoundException("The City doesn't exist yet...");
        }

        $city = $city->getPlace();

        $repositry = $this->getDoctrine()->getRepository('Church:Place');

        $state = $repositry->findState($city->getID());

        $country = $repositry->findCountry($city->getID());

        $variables = array(
          'city' => $city,
          'state' => $state,
          'country' => $country,
        );

        return $this->render('place/city.html.twig', $variables);

    }

}
