<?php

namespace Church\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Church\Bundle\UserBundle\Entity\User;
use Church\Bundle\UserBundle\Entity\Email;
use Church\Bundle\UserBundle\Entity\EmailVerify;

class VerificationController extends Controller
{
    public function emailAction(Request $request, $user_id, $token)
    {

      return;

    }
}
