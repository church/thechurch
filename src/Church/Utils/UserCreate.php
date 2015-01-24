<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;

use Church\Entity\User;
use Church\Entity\Email;

class UserCreate {

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Create a new User from an Email.
     *
     * @param string $email_address Valid email address.
     *
     * @return User Newly created user object.
     */
    public function createFromEmail(Email $email)
    {

      $em = $this->getDoctrine()->getManager();

      // Create a new stub user.
      $user = new User();

      // Save the User
      $em->persist($user);
      $em->flush();

      // Set the Email
      $email->setUser($user);
      $user->setPrimaryEmail($email);

      // Save the Email
      $em->persist($email);
      $em->persist($user);
      $em->flush();

      return $user;

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


}
