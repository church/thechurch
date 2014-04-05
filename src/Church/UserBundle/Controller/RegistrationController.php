<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Church\UserBundle\Entity\User;
use Church\UserBundle\Entity\Email;
use Church\UserBundle\Form\Type\RegistrationEmailType;
use Church\UserBundle\Form\Model\RegistrationEmail;

class RegistrationController extends Controller
{
    public function emailAction(Request $request)
    {

    		// Build the Registration Form
        $form = $this->createForm(new RegistrationEmailType(), new RegistrationEmail());

        // If this Form has been completed
        if ($request->isMethod('POST')) {

          // Bind the Form to the request
        	$form->bind($request);

        	// Check to make sure the form is valid before procceding
        	if ($form->isValid()) {

            $repositry = $this->getDoctrine()->getRepository('ChurchUserBundle:Email');

        	  // Get the form data
        	  $register = $form->getData();

            if ($email = $repositry->findOneByEmail($register->getEmail())) {

              // @TODO: Do Something if the Email is already found.
              return;

            }
            else {

              // @TODO: Insert the Email & Validation Code into the Database.

              // Get the Dispatcher Service.
              $verify_email = $this->get('church_user.verify_email');

              $verify_email->sendVerification($register->getEmail());

              // @TODO: Move the User Insertion to post-initial email confirmation.
              //        This should keep the Users clean.

              /*
              $em = $this->getDoctrine()->getManager();

              $user = new User();

              // Save the User
              $em->persist($user);
              $em->flush();

              // Create the Email
              $email = new Email();
              $email->setUser($user);
              $email->setEmail($register->getEmail());
              $user->setPrimaryEmail($email);

              // Save the Email
              $em->persist($email);
              $em->persist($user);
              $em->flush();
              */

            }

	        	// Redirect the User
	        	return $this->render('ChurchUserBundle:Registration:email.html.twig', array(
  	            'form' => $form->createView(),
  	        ));
	        	// return $this->redirect($this->generateUrl('church_user_register'));

        	}

        }
        // If the form hasn't yet been completed
        else {

	        return $this->render('ChurchUserBundle:Registration:email.html.twig', array(
	            'form' => $form->createView(),
	        ));

        }
    }
}
