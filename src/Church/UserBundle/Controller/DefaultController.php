<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Church\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Church\UserBundle\Form\Type\RegisterType;

class DefaultController extends Controller
{
    public function registerAction(Request $request)
    {
    		// Create a new User
    		$user = new User();
    		
    		// Build the Registration Form
        $form = $this->createForm(new RegisterType(), $user);
        
        
        // If this Form has been completed
        if ($request->isMethod('POST')) {
        
        	$form->bind($request);
        	
        	if ($form->isValid()) {
        		
        		// Encrypt the Password
        		$factory = $this->get('security.encoder_factory');
        		$encoder = $factory->getEncoder($user);
        		$password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
        		$user->setPassword($password);
        		
        		// Save the User
	        	$em = $this->getDoctrine()->getManager();
	        	$em->persist($user);
	        	$em->flush();
	        	
	        	// Redirect the User
	        	return $this->redirect($this->generateUrl('church_user_register'));
	        	
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
