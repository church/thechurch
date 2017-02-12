<?php

namespace Church\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Church\Entity\User\User;
use Church\Entity\User\Login;
use Church\Entity\User\EmailVerify;
use Church\Entity\User\PhoneVerify;
use Church\Form\Type\LoginType;
use Church\Form\Type\VerifyType;
use Church\Form\Model\Verify;
use Church\Form\Type\NameType;
use Church\Form\Model\Name;
use Church\Form\Type\FaithType;
use Church\Form\Model\Faith;

/**
 * @Route(
 *    "/user",
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
   * @Route("/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user, Request $request) : Response
    {
        return $this->reply($user, $request->getRequestFormat());
    }

    /**
     * @Route("/name", name="user_name")
     * @Security("!has_role('ROLE_NAME') and (has_role('ROLE_EMAIL') or has_role('ROLE_PHONE'))")
     */
    public function nameAction(Request $request)
    {
        // Build the Verification Form
        $form = $this->createForm(new NameType(), new Name());

        // Handle the Form Request.
        $form->handleRequest($request);

        // If this Form has been completed
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->getData();

            $doctrine = $this->getDoctrine();
            $em = $doctrine->getEntityManager();

            $user = $this->getUser();
            $user->setFirstName($name->getFirstName());
            $user->setLastName($name->getLastName());

            $em->persist($user);
            $em->flush();

            $this->get('security.context')->getToken()->setAuthenticated(false);

            return $this->redirect($this->generateUrl('index'));
        }

        return $this->render('user/name.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/faith", name="user_faith")
     * @Security("has_role('ROLE_NAME')")
     */
    public function faithAction(Request $request)
    {
        // Build the Verification Form
        $form = $this->createForm(new FaithType(), new Faith());

        // Handle the Form Request.
        $form->handleRequest($request);

        // If this Form has been completed
        if ($form->isSubmitted() && $form->isValid()) {
            $faith = $form->getData();

            $doctrine = $this->getDoctrine();
            $em = $doctrine->getEntityManager();

            $user = $this->getUser();
            $user->setFaith($faith->getFaith());

            $em->persist($user);
            $em->flush();

            $this->get('security.context')->getToken()->setAuthenticated(false);

            return $this->redirect($this->generateUrl('index'));
        }

        return $this->render('user/faith.html.twig', array(
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ));
    }
    /*
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
    */
}
