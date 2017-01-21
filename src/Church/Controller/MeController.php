<?php

namespace Church\Controller;

use Church\Entity\User\Login;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route(
 *    service="church.controller_me",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class MeController extends Controller
{
  /**
   * @Route("/me.{_format}")
   */
    public function showAction(Request $request) : Response
    {
        if (!$this->isLoggedIn()) {
            throw new NotFoundHttpException('Not Logged In');
        }

        return $this->reply($this->getUser(), $request->getRequestFormat());
    }

    /**
     * @Route("/login", name="user_login")
     * @Method("POST")
     * @Security("!has_role('ROLE_USER')")
     */
    public function loginAction(Request $request)
    {
        $login = $this->serializer->deserialize(
            $request->getContent(),
            Login::class,
            $request->getRequestFormat()
        );

        $this->validate($login);

        // @TODO Make this rest of this work. Perhaps a Normalizer should
        //       figure out if it's a email or a phone number? Then the
        //       verification logic should probably move into this class since
        //       this is the only place it will be used.
        dump($login->getUserName());
        exit;

        // If this Form has been completed
        if ($form->isSubmitted() && $form->isValid()) {
            $login = $form->getData();

            $validator = $this->get('church.validator.login');

            if ($validator->isEmail($login->getUsername())) {
                $verify = $this->get('church.verify_create')
                               ->createEmail($login->getUsername());

                // Send the Verification.
                $this->get('church.verify_send')->sendEmail($verify);

                return $this->redirect($this->generateUrl('user_verify', array(
                    'type' => 'e',
                    'token' => $verify->getToken(),
                )));
            } elseif ($validator->isPhone($login->getUsername())) {
                $verify = $this->get('church.verify_create')
                               ->createPhone($login->getUsername());

                // Send the Verification.
                $this->get('church.verify_send')->sendSMS($verify);

                return $this->redirect($this->generateUrl('user_verify', array(
                    'type' => 'p',
                    'token' => $verify->getToken(),
                )));
            }
        }

        return $this->render('user/login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
