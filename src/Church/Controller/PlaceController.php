<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\NoResultException;

use Church\Entity\City;
use Church\Entity\Place;

class PlaceController extends Controller
{

    /**
     * @Route("/nearby", name="place_nearby")
     * @Security("has_role('ROLE_FAITH')")
     */
    public function nearbyAction()
    {
      return $this->render('place/nearby.html.twig');
    }

    /**
     * @Route("/{slug}", name="place_city")
     * @Security("has_role('ROLE_FAITH')")
     */
    public function cityAction($slug)
    {

        $repositry = $this->getDoctrine()->getRepository('Church:City');

        try {
          $city = $repositry->findCityBySlug($slug);
        } catch (NoResultException $e) {
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
