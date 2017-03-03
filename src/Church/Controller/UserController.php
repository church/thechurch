<?php

namespace Church\Controller;

use SampleClass;
use Church\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route(
 *    service="church.controller_user",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class UserController extends Controller
{

  /**
   * @Route("/user/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user, Request $request) : Response
    {
        $roles = [];
        if ($this->isNeighbor($user)) {
            $roles[] = 'neighbor';
        }
        return $this->serializer->serialize($user, $request->getRequestFormat(), $roles);
    }


    /**
     * Determine if the current user and the requested user are in the same
     * place.
     *
     * @param User $user
     */
    protected function isNeighbor(User $user) : bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        if (!$this->getUser()->getLocation()) {
            return false;
        }

        if (!$this->getUser()->getLocation()->getPlace()) {
            return false;
        }

        if (!$user->getLocation()) {
            return false;
        }

        if (!$user->getLocation()->getPlace()) {
            return false;
        }

        return $this->getUser()->getLocation()->getPlace()->getId() === $user->getLocation()->getPlace()->getId();
    }
}
