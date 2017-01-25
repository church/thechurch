<?php

namespace Church\Utils;

use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use RandomLib\Generator as RandomGenerator;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\EmailVerify;
use Church\Entity\User\Phone;
use Church\Entity\User\PhoneVerify;
use Church\Validator\Constraints\LoginValidator as Validator;

class VerifyCreate
{

    // @TODO Move the phone verification into it's own class.

    /**
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    protected $doctrine;

    /**
     * @var \RandomLib\Generator
     */
    protected $random;

    /**
     * @var \Church\Validator\Constraints\LoginValidator
     */
    protected $validator;


    public function __construct(
        Doctrine $doctrine,
        RandomGenerator $random,
        Validator $validator
    ) {
        $this->doctrine = $doctrine;
        $this->random = $random;
        $this->validator = $validator;
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

        $em = $this->doctrine->getManager();

        $parsed = $this->validator->getPhone()->parse($phone_number, 'US');

        $phone_number = $parsed->getCountryCode().$parsed->getNationalNumber();

        // Get the existig email from the database.
        $phone = $this->findExistingPhone($phone_number);

        // If there is ane email, then there's also a user.
        if (!$phone) {
            $phone = new Phone();
            $phone->setPhone($phone_number);

            $user = $em->getRepository(User::class)->createFromPhone($phone);
        }

        $verify = new PhoneVerify();
        $random = $this->random;
        $verify->setToken($this->getUniqueToken('Church:User\PhoneVerify'));
        $verify->setCode($this->getUniqueCode('Church:User\PhoneVerify'));
        $verify->setPhone($phone);
        $phone->setVerify($verify);

        $em->persist($phone);
        $em->persist($verify);
        $em->flush();

        return $verify;
    }

    /**
     * Finds an Existing Phone Number.
     *
     * @param string $phone_number Valid phone number.
     *
     * @return mixed Existing Phone object or NULL.
     */
    private function findExistingPhone($phone_number) :? Phone
    {

        $em = $this->doctrine->getManager();

        // Get the existig email from the database.
        $repository = $this->doctrine->getRepository(Phone::class);

        // If there is ane email, then there's also a user.
        if ($phone = $repository->findOneByPhone($phone_number)) {
            $repository = $this->doctrine->getRepository(PhoneVerify::class);

            // If one is found, destroy it so a new one can be issued.
            if ($verify = $repository->findOneByPhone($phone_number)) {
                $em->remove($verify);
                $em->flush();
            }
        }

        return $phone;
    }

    /**
     * Gets a Unique Token
     *
     * @param string $entity Doctrine entity to search against.
     *
     * @return string A unique token.
     */
    private function getUniqueToken($entity)
    {
        $repository = $this->doctrine->getRepository($entity);
        $random = $this->random;

        do {
            $token = $random->generateString(6, $random::CHAR_ALNUM);
        } while ($repository->findOneByToken($token));

        return $token;
    }

    /**
     * Gets a Unique Code
     *
     * @param string $entity Doctrine entity to search against.
     *
     * @return string A unique code.
     */
    private function getUniqueCode($entity)
    {
        $repository = $this->doctrine->getRepository($entity);
        $random = $this->random;

        do {
            $code = $random->generateString(6, $random::CHAR_DIGITS);
        } while ($repository->findOneByCode($code));

        return $code;
    }
}
