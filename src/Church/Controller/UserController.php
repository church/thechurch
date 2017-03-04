<?php

namespace Church\Controller;

use Church\Entity\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @Route("/user.{_format}")
     * @Method("GET")
     *
     * @param Request $request
     */
    public function indexAction(Request $request) : Response
    {
        if (!$request->query->has('username')) {
            throw new BadRequestHttpException("Missing Username Paramter");
        }

        $repository = $this->doctrine->getRepository(User::class);

        $user = $repository->findOneByUsername($request->query->get('username'));

        if (!$user) {
            throw new NotFoundHttpException("No user found");
        }

        return $this->showAction($user, $request);
    }

  /**
   * @Route("/user/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user, Request $request) : Response
    {
        if (!$user->isEnabled()) {
            throw new NotFoundHttpException("User account is disabled");
        }

        $roles = [];
        if ($this->isLoggedIn()) {
            if ($this->getUser()->isEqualTo($user)) {
                $roles[] = 'me';
            } elseif ($this->getUser()->isNeighbor($user)) {
                $roles[] = 'neighbor';
            }
        }

        return $this->serializer->respond($user, $request->getRequestFormat(), $roles);
    }
}
