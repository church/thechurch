<?php

namespace Church\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $error = NULL;

        $user = $this->getUser();

        if (!empty($user)) {
          $name = $user->getUsername();
        }
        else {
          $name = "nope!";
        }

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );

            $this->get('session')->getFlashBag()->add(
              'error',
              SecurityContext::AUTHENTICATION_ERROR
            );

        } else {

            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);

            if ($error) {

              $this->get('session')->getFlashBag()->add(
                'error',
                $error->getMessage()
              );

            }

            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'ChurchUserBundle:Security:login.html.twig',
            array(
                // last username entered by the user
                'session' => $name,
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            )
        );
    }
}
