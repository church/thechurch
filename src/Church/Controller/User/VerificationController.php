<?php

namespace Church\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Church\Entity\User;
use Church\Entity\Email;
use Church\Entity\EmailVerify;

class VerificationController extends Controller
{
    /**
     * @Route("/{user_id}/verify/email/{token}", name="verify_email")
     */
    public function emailAction(Request $request, User $user, $token)
    {

      return;

    }
}
