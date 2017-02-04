<?php

namespace Church\Utils\User;

use Church\Entity\User\User;
use Church\Entity\User\Phone;
use Church\Entity\User\PhoneVerify;
use Church\Entity\Message\SMSMessage;
use Church\Entity\User\VerifyInterface;
use Church\Utils\Dispatcher\DispatcherInterface;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use RandomLib\Generator as RandomGenerator;
use libphonenumber\PhoneNumberUtil;

/**
 * Phone Verification Utility.
 */
class PhoneVerification implements VerificationInterface
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
     * @var \libphonenumber\PhoneNumberUtil
     */
    protected $parser;

    /**
     * @var \Church\Utils\Dispatcher\DispatcherInterface
     */
    protected $dispatcher;

    /**
     * Create a new Phone Verification.
     *
     * @param Doctrine $doctrine
     * @param RandomGenerator $random
     * @param PhoneNumberUtil $parser
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(
        Doctrine $doctrine,
        RandomGenerator $random,
        PhoneNumberUtil $parser,
        DispatcherInterface $dispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->random = $random;
        $this->parser = $parser;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create a Verification from an email address.
     *
     * @param string $phone_number Valid phone number address.
     *
     * @return PhoneVerify Newly created verify object.
     */
    public function create(string $phone_number) : VerifyInterface
    {
        $em = $this->doctrine->getManager();

        $parsed = $this->parser->parse($phone_number, 'US');

        $phone_number = $parsed->getCountryCode() . $parsed->getNationalNumber();

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
        $verify->setToken($this->getUniqueToken());
        $verify->setCode($this->getUniqueCode());
        $verify->setPhone($phone);
        $phone->setVerify($verify);

        $em->persist($phone);
        $em->persist($verify);
        $em->flush();

        return $verify;
    }

    /**
     * {@inheritdoc}
     */
    public function send(VerifyInterface $verify) : bool
    {
        $message = new SMSMessage();

        $params = array(
          'token' => $verify->getToken(),
          'code' => $verify->getCode(),
        );

        // @TODO get rid of the router and generate the url ourselves.
        $link = $this->getRouter()->generate('user_verify_phone', $params, true);

        $message->setTo($verify->getPhone()->getPhone());

        $message->addTextLine('thechur.ch');
        $message->addTextLine('Login Code: '.$verify->getCode());
        $message->addTextLine('');
        $message->addTextLine($link);

        return $this->dispatcher->send($message);
    }

    /**
     * Finds an Existing Phone Number.
     *
     * @param string $phone_number Valid phone number.
     *
     * @return mixed Existing Phone object or NULL.
     */
    private function findExisting(string $phone_number) :? Phone
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
     * @return string A unique token.
     *
     * @deprecated Attempt to insert and catch the exception rather than looking
     *             for an existing item which may be a race condition.
     */
    private function getUniqueToken()
    {
        $repository = $this->doctrine->getRepository(PhoneVerify::class);
        $random = $this->random;

        do {
            $token = $random->generateString(6, $random::CHAR_ALNUM);
        } while ($repository->findOneByToken($token));

        return $token;
    }

    /**
     * Gets a Unique Code
     *
     * @deprecated Attempt to insert and catch the exception rather than looking
     *             for an existing item which may be a race condition.
     */
    private function getUniqueCode()
    {
        $repository = $this->doctrine->getRepository(PhoneVerify::class);
        $random = $this->random;

        do {
            $code = $random->generateString(6, $random::CHAR_DIGITS);
        } while ($repository->findOneByCode($code));

        return $code;
    }
}
