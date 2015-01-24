<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use RandomLib\Generator as RandomGenerator;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\EmailVerify;
use Church\Entity\User\Phone;
use Church\Entity\User\PhoneVerify;

class VerifyCreate {

    private $doctrine;

    private $random;

    private $user_create;

    public function __construct(Doctrine $doctrine,
                                RandomGenerator $random,
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
      $email = $this->findExistingEmail($email_address);

      // If there is ane email, then there's also a user.
      if (!$email) {

        $email = new Email();
        $email->setEmail($email_address);

        $user = $this->getUserCreate()->createFromEmail($email);

      }

      $verify = new EmailVerify();
      $verify->setToken($this->getRandom()->generateInt(0, 999999));
      $verify->setEmail($email);
      $email->setVerify($verify);

      $em->persist($email);
      $em->persist($verify);
      $em->flush();

      return $verify;

    }

    /**
     * Create a Verification from an phone number.
     *
     * @param string $phone_number Valid phone number.
     *
     * @return PhoneVerify Newly created verify object.
     */
    public function createPhone($phone_number)
    {

      $em = $this->getDoctrine()->getManager();

      // Get the existig email from the database.
      $phone = $this->findExistingPhone($phone_number);

      // If there is ane email, then there's also a user.
      if (!$phone) {

        $phone = new Phone();
        $phone->setPhone($phone_number);

        $user = $this->getUserCreate()->createFromPhone($phone);

      }

      $verify = new PhoneVerify();
      $verify->setToken($this->getRandom()->generateInt(0, 999999));
      $verify->setPhone($phone);
      $phone->setVerify($verify);

      $em->persist($phone);
      $em->persist($verify);
      $em->flush();

      return $verify;

    }

    /**
     * Finds an Existing Email.
     *
     * @param string $email_address Valid email_addressr.
     *
     * @return mixed Existing Email object or NULL.
     */
    private function findExistingEmail($email_address)
    {

      $em = $this->getDoctrine()->getManager();

      // Get the existig email from the database.
      $repository = $this->getDoctrine()->getRepository('Church:User\Email');

      // If there is ane email, then there's also a user.
      if ($email = $repository->findOneByEmail($email_address)) {

        // Find any existing verification.
        $repository = $this->getDoctrine()->getRepository('Church:User\EmailVerify');

        // If one is found, destroy it so a new one can be issued.
        if ($verify = $repository->findOneByEmail($email_address)) {

          // Delete the verification.
          $em->remove($verify);
          $em->flush();

        }

      }

      return $email;

    }

    /**
     * Finds an Existing Phone Number.
     *
     * @param string $phone_number Valid phone number.
     *
     * @return mixed Existing Phone object or NULL.
     */
    private function findExistingPhone($phone_number)
    {

      $em = $this->getDoctrine()->getManager();

      // Get the existig email from the database.
      $repository = $this->getDoctrine()->getRepository('Church:User\Phone');

      // If there is ane email, then there's also a user.
      if ($phone = $repository->findOneByPhone($phone_number)) {

        // Find any existing verification.
        $repository = $this->getDoctrine()->getRepository('Church:User\PhoneVerify');

        // If one is found, destroy it so a new one can be issued.
        if ($verify = $repository->findOneByPhone($phone_number)) {

          // Delete the verification.
          $em->remove($verify);
          $em->flush();

        }

      }

      return $phone;

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