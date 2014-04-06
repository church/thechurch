<?php

namespace Church\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Church\UserBundle\Entity\User;
use Church\UserBundle\Entity\Email;
use Church\UserBundle\Entity\EmailVerify;

class VerificationController extends Controller
{
    public function emailAction(Request $request, $user_id, $token)
    {

      return;

    }
}
