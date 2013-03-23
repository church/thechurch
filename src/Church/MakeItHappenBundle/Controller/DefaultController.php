<?php

namespace Church\MakeItHappenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Church\MakeItHappenBundle\Form\Type\DonateType;
use Church\MakeItHappenBundle\Form\Model\Donate;

class DefaultController extends Controller
{
    
    public function indexAction(Request $request)
    {
        
        $params = array(
          'stripe' => $this->container->parameters['church_make_it_happen']['stripe']['publishable_key'],
        );
        
        return $this->render('ChurchMakeItHappenBundle:Default:index.html.twig', $params);
    }
    
    
    public function donateAction(Request $request)
    {
        
        // Build the Donation form.
        $form = $this->createForm(new DonateType(), new Donate());
                
        // If this Form has been completed
        if ($request->isMethod('POST')) {
        
          // Bind the Form to the request
          $form->bind($request);
          
          // Check to make sure the form is valid before procceding
          if ($form->isValid()) {
          
            $donate = $form->getData();
            
            if ($request->isXmlHttpRequest()) {
            
            }
            else {
              return $this->redirect($this->generateUrl('church_make_it_happen_donate'));
            }
            
          }
          else {
            
            if ($request->isXmlHttpRequest()) {
            
              $data = array(
                'error' => $form->getErrors(),
              );
              
              $response = new JsonResponse();
              $response->setData($data);
              
              return $response;
              
            }
            
          }
          
        }
        
        $params = array(
          'form' => $form->createView(),
        );
        
        return $this->render('ChurchMakeItHappenBundle:Default:donate.html.twig', $params);
    }
}
