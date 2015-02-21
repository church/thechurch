<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route(
     *   "/nearby/{latitude}/{longitude}",
     *   name="place_nearby_location",
     *   options={
     *     "expose"=true
     *   }
     * )
     * @Security("has_role('ROLE_FAITH')")
     */
    public function nearbyLocationAction($latitude, $longitude)
    {
      $data = array();

      // @TODO The first thing we need to do is get all the location data from
      // either the database or Yahoo! from there we can save all the data in
      // the database (if needed). At the end of the day, we need to get a
      // WOEID the user is closest to. The posts that are returned will be from
      // that WOEID.

      $data['hello'] = 'Hello '.$latitude.' '.$longitude;

      return new JsonResponse($data);
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
