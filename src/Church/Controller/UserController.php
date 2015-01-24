<?php

namespace Church\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Church\Entity\User\User;
use Church\Entity\User\EmailVerify;
use Church\Form\Type\LoginType;
use Church\Form\Model\Login;

class UserController extends Controller
{

    /**
     * @Route("/u/login", name="user_login")
     */
    public function loginAction(Request $request)
    {

        // Build the Registration Form
        $form = $this->createForm(new LoginType(), new Login());

        // Handle the Form Request.
        $form->handleRequest($request);

        // If this Form has been completed
        if ($form->isSubmitted() && $form->isValid()) {

          // Get the form data
          $login = $form->getData();

          $validator = $this->get('church.validator.login');

          if ($validator->isEmail($login->getUsername())) {

            // Create the Verification.
            $verify = $this->get('church.verify_create')
                           ->createEmail($login->getUsername());

            // Send the Verification.
            $this->get('church.verify_send')->sendEmail($verify);

          }
          elseif ($validator->isPhone($login->getUsername())) {

            // Create the Verification.
            $verify = $this->get('church.verify_create')
                           ->createPhone($login->getUsername());

            // Send the Verification.
            $this->get('church.verify_send')->sendSMS($verify);

          }

          // Redirect the User
          return $this->render('user/login.html.twig', array(
              'form' => $form->createView(),
          ));
          // return $this->redirect($this->generateUrl('church_user_register'));

        }

        return $this->render('user/login.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /**
     * @Route("/u/{user_id}/v/e/{token}", name="user_verify_email")
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
    public function verifyEmailAction(Request $request, User $user,  $token = NULL)
    {

      return;

    }

    /**
     * @Route("/user/register_old", name="register_old")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {

    		// Build the Registration Form
         $form = $this->createForm(new RegistrationType(), new Registration());


        // If this Form has been completed
        if ($request->isMethod('POST')) {

          // Bind the Form to the request
        	$form->bind($request);

        	// Check to make sure the form is valid before procceding
        	if ($form->isValid()) {

        	  // Get the form data
        	  $register = $form->getData();
        	  $user = new User();
        	  $user->setName($register->getName());
        	  $user->setUsername($register->getUsername());
        	  $user->setAddress($register->getAddress());

        		// Encrypt the Password
        		$factory = $this->get('security.encoder_factory');
        		$encoder = $factory->getEncoder($user);
        		$password = $encoder->encodePassword($register->getPassword(), $user->getSalt());
        		$user->setPassword($password);

        		// Find the User's Place
        		$em = $this->getDoctrine()->getManager();
        		$finder = $this->get('church.place_finder');
        		$place = $finder->findSavePlace($register->getAddress());

        		// Before setting the Place to the user, get it from the Database
      		  $repository = $this->getDoctrine()->getRepository('Church\Entity\Place');
      		  $place = $repository->find($place['woeid']);

        		if (!empty($place)) {
        		  $user->setLatitude($place->getLatitude());
        		  $user->setLongitude($place->getLongitude());

          		$user->setPlace($place);
        		}

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


	        	// Redirect the User
	        	return $this->render('user/register.html.twig', array(
  	            'form' => $form->createView(),
  	        ));
	        	// return $this->redirect($this->generateUrl('church_user_register'));

        	}

        }
        // If the form hasn't yet been completed
        else {
	        return $this->render('user/register.html.twig', array(
	            'form' => $form->createView(),
	        ));
        }
    }
}
