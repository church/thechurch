<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Church\UserBundle\Entity\User;
use Church\UserBundle\Entity\Email;
use Church\UserBundle\Form\Type\RegistrationType;
use Church\UserBundle\Form\Model\Registration;

class DefaultController extends Controller
{
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
        		$finder = $this->get('church_place.place_finder');
        		$place = $finder->findPlace($this, $register->getAddress());
        		
        		if (!empty($place)) {
          		$user->setPlace($place);
        		}
        		        		        		
        		// Save the User
	        	$em = $this->getDoctrine()->getManager();
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
	        	return $this->render('ChurchUserBundle:Default:register.html.twig', array(
  	            'form' => $form->createView(),
  	        ));
	        	// return $this->redirect($this->generateUrl('church_user_register'));
	        	
        	}
        	
        }
        // If the form hasn't yet been completed
        else {
	        return $this->render('ChurchUserBundle:Default:register.html.twig', array(
	            'form' => $form->createView(),
	        ));
        }
    }
}
