<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;
use RandomLib\Generator;

use Church\Entity\User;
use Church\Entity\Email;
use Church\Entity\EmailVerify;

class VerifyCreate {

    private $doctrine;

    private $random;

    private $user_create;

    public function __construct(RegistryInterface $doctrine,
                                Generator $random,
                                UserCreate $user_create)
    {
        $this->doctrine = $doctrine;
        $this->random = $random;
        $this->user_create = $user_create;
    }

    /**
     * Create a Verification from an email address.
     *
     * @param string $email_address Valid email address.
     *
     * @return EmailVerify Newly created verify object.
     */
    public function createEmail($email_address)
    {

      $em = $this->getDoctrine()->getManager();

      // Get the existig email from the database.
      $repository = $this->getDoctrine()->getRepository('Church:Email');

      // If there is ane email, then there's also a user.
      if ($email = $repository->findOneByEmail($email_address)) {

        // Find any existing verification.
        $repository = $this->getDoctrine()->getRepository('Church:EmailVerify');

        // If one is found, destroy it so a new one can be issued.
        if ($verify = $repository->findOneByEmail($email_address)) {

          // Delete the verification.
          $em->remove($verify);
          $em->flush();

        }

      }
      else {

        $email = new Email();
        $email->setEmail($email_address);

        $user = $this->getUserCreate()->createFromEmail($email);

      }

      $verify = new EmailVerify();
      $verify->setToken($this->getRandom()->generateInt(0, 999999));
      $verify->setEmail($email);

      $em->persist($verify);
      $em->flush();

      return $verify;

    }

    public function setDoctrine($doctrine)
    {
      $this->doctrine = $doctrine;
      return $this;
    }

    public function getDoctrine()
    {
      return $this->doctrine;
    }

    public function setRandom($random)
    {
      $this->random = $random;
      return $this;
    }

    public function getRandom()
    {
      return $this->random;
    }

    public function setUserCreate($user_create)
    {
      $this->user_create = $user_create;
      return $this;
    }

    public function getUserCreate()
    {
      return $this->user_create;
    }


}
