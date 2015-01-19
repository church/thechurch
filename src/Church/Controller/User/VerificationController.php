<?php

namespace Church\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Church\Bundle\UserBundle\Entity\User;
use Church\Bundle\UserBundle\Entity\Email;
use Church\Bundle\UserBundle\Entity\EmailVerify;

class VerificationController extends Controller
{
    /**
     * @Route(
     *   "/{user_id}/verify/email/{token}",
     *   name="verify_email",
     *   requirements: {
     *    "user_id": "\d+"
     *   }
     * )
     * @Method("POST")
     */
    public function emailAction(Request $request, User $user, $token)
    {

      return;

    }
}
