<?php

namespace Church\Utils\User;

use Church\Entity\Message\EmailMessage;
use Church\Entity\User\VerifyInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use RandomLib\Generator as RandomGenerator;

use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\EmailVerify;
use Church\Utils\Dispatcher\DispatcherInterface;

/**
 * Email Verification.
 */
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
     * @var \Church\Utils\Dispatcher\DispatcherInterface
     */
    protected $dispatcher;

    /**
     * Create the Email Verification.
     *
     * @param Doctrine $doctrine
     * @param RandomGenerator $random
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(
        Doctrine $doctrine,
        RandomGenerator $random,
        DispatcherInterface $dispatcher
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
    public function create(string $email_address) : VerifyInterface
    {
        $em = $this->doctrine->getManager();

        // Get the existig email from the database.
        $email = $this->findExisting($email_address);

        // If there is ane email, then there's also a user.
        if (!$email) {
            $email = new Email([
                'email' => $email_address,
            ]);

            $user = $em->getRepository(User::class)->createFromEmail($email);
        }

        $saved = false;
        while (!$saved) {
            try {
                $verify = new EmailVerify([
                    'email' => $email,
                    'token' => $this->random->generateString(6, $this->random::CHAR_LOWER | $this->random::CHAR_DIGITS),
                    'code' => $this->random->generateString(6, $this->random::CHAR_DIGITS),
                ]);

                $email->setVerify($verify);
                $em->persist($verify);
                $em->flush();
                $saved = true;
            } catch (UniqueConstraintViolationException $e) {
                // Try again.
            }
        }

        return $verify;
    }

    /**
     * {@inheritdoc}
     */
    public function send(VerifyInterface $verify) : bool
    {
        $message = new EmailMessage();

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
}
