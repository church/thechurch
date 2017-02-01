<?php

namespace Church\Utils\User;

use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use RandomLib\Generator as RandomGenerator;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\EmailVerify;
use Church\Entity\Message\Email as Message;
use Church\Utils\Dispatcher\Email as Dispatcher;

class EmailVerification implements VerificationInterface
{

    /**
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    protected $doctrine;

    /**
     * @var \RandomLib\Generator
     */
    protected $random;

    /**
     * @var \Church\Utils\Dispatcher\Email
     */
    protected $dispatcher;

    public function __construct(
        Doctrine $doctrine,
        RandomGenerator $random,
        Dispatcher $dispatcher
    ) {
        $this->doctrine = $doctrine;
        $this->random = $random;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Create a Verification from an email address.
     *
     * @param string $email_address Valid email address.
     *
     * @return EmailVerify Newly created verify object.
     */
    public function create(string $email_address) : EmailVerify
    {
        $em = $this->doctrine->getManager();

        // Get the existig email from the database.
        $email = $this->findExisting($email_address);

        // If there is ane email, then there's also a user.
        if (!$email) {
            $email = new Email();
            $email->setEmail($email_address);

            $user = $em->getRepository(User::class)->createFromEmail($email);
        }

        $verify = new EmailVerify();

        $verify->setToken($this->getUniqueToken(Email::class));
        $verify->setCode($this->getUniqueCode(EmailVerify::class));
        $verify->setEmail($email);
        $email->setVerify($verify);

        $em->persist($email);
        $em->persist($verify);
        $em->flush();

        return $verify;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailVerify $verify) : boolean
    {
        $message = new Message();

        $params = array(
          'token' => $verify->getToken(),
          'code' => $verify->getCode(),
        );

        // Build the Message.
        $message->addTo($verify->getEmail()->getEmail());
        $message->setSubject('Confirm Your Email');

        // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
        $text = "Please visit the following location to verify your email.\n";
        $text .= $this->getRouter()->generate('user_verify_email', $params, true);

        $message->setText($text);

        // Send the Message using Async.
        return $this->dispatcher->send($message);
    }


    /**
     * Finds an Existing Email.
     *
     * @param string $email_address Valid email_addressr.
     *
     * @return mixed Existing Email object or NULL.
     */
    protected function findExisting(string $email_address) :? Email
    {

        $em = $this->doctrine->getManager();

        // Get the existig email from the database.
        $repository = $this->doctrine->getRepository(Email::class);

        // If there is ane email, then there's also a user.
        if ($email = $repository->findOneByEmail($email_address)) {
            $repository = $this->doctrine->getRepository(EmailVerify::class);

            // If one is found, destroy it so a new one can be issued.
            if ($verify = $repository->findOneByEmail($email_address)) {
                $em->remove($verify);
                $em->flush();
            }
        }

        return $email;
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
        $repository = $this->doctrine->getRepository(EmailVerify::class);
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
     *
     * @deprecated Attempt to insert and catch the exception rather than looking
     *             for an existing item which may be a race condition.
     */
    private function getUniqueCode()
    {
        $repository = $this->doctrine->getRepository(EmailVerify::class);
        $random = $this->random;

        do {
            $code = $random->generateString(6, $random::CHAR_DIGITS);
        } while ($repository->findOneByCode($code));

        return $code;
    }
}
