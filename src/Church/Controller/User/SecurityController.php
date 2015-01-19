<?php

namespace Church\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Church\Form\Type\LoginType;
use Church\Form\Model\Login;

class SecurityController extends Controller
{
    /**
     * @Route("/user/login", name="login")
     * @Method("POST")
     */
    public function loginAction()
    {

        // Build the Login Form
        $form = $this->createForm(new LoginType(), new Login());

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
            'user/login.html.twig',
            array(
                // last username entered by the user
                'form' => $form->createView(), 
                'session' => $name,
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            )
        );
    }
}
