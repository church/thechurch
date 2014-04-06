<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Church\UserBundle\Entity\User;
use Church\UserBundle\Entity\Email;
use Church\UserBundle\Entity\EmailVerify;
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

            $em = $this->getDoctrine()->getManager();
            $email_repository = $this->getDoctrine()->getRepository('ChurchUserBundle:Email');
            $verify_repository = $this->getDoctrine()->getRepository('ChurchUserBundle:EmailVerify');

        	  // Get the form data
        	  $register = $form->getData();

            if ($verify = $verify_repository->findOneByEmail($register->getEmail())) {

              // Delete the verification.
              $em->remove($verify);
              $em->flush();

              $email = $email_repository->findOneByEmail($register->getEmail());

            }
            else {

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

            }


            $verify = new EmailVerify();
            $verify->setEmail($email);

            // Save the Verification.
            $em->refresh($email);
            $em->persist($verify);
            $em->flush();

            // Get the Dispatcher Service.
            $verify_email = $this->get('church_user.verify_email');

            // Send the Verification Email.
            $verify_email->sendVerification($verify);

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
