<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\Phone;

class UserCreate
{

    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Create a new User from an Email.
     *
     * @param Email $email Valid email object.
     *
     * @return User Newly created user object.
     */
    public function createFromEmail(Email $email)
    {
        $em = $this->getDoctrine()->getManager();

        // Create a new stub user.
        $user = $this->createStub();

        // Set the Email
        $email->setUser($user);
        $user->setPrimaryEmail($email);

        // Save the Email
        $em->persist($email);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Create a new User from a Phone.
     *
     * @param Phone $phone Valid phone object.
     *
     * @return User Newly created user object.
     */
    public function createFromPhone(Phone $phone)
    {
        $em = $this->getDoctrine()->getManager();

        // Create a new stub user.
        $user = $this->createStub();

        // Set the Phone
        $phone->setUser($user);
        $user->setPrimaryPhone($phone);

        // Save the phone.
        $em->persist($phone);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Create a stub User.
     *
     * @return User Newly created user object.
     */
    private function createStub()
    {
        $em = $this->getDoctrine()->getManager();

        // Create a new stub user.
        $user = new User();

        // Save the User
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
