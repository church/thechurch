<?php

namespace Church\Utils;

use Symfony\Component\Routing\RouterInterface as Router;

use Church\Entity\User\EmailVerify;
use Church\Entity\User\PhoneVerify;
use Church\Utils\Dispatcher\Email as EmailDispatcher;
use Church\Utils\Dispatcher\SMS as SMSDispatcher;
use Church\Message\Email as EmailMessage;
use Church\Message\SMS as SMSMessage;

class VerifySend
{

    // @TODO Move this into the appropriate Verify utility.

    private $dispatcher;

    private $router;

    public function __construct(
        EmailDispatcher $email,
        SMSDispatcher $sms,
        Router $router
    ) {
        $this->dispatcher = array(
          'email' => $email,
          'sms' => $sms,
        );
        $this->router = $router;
    }


    public function sendEmail(EmailVerify $verify)
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
        return $this->getDispatcher('email')->send($message);
    }

    public function sendSMS(PhoneVerify $verify)
    {
        $message = new SMSMessage();

        $params = array(
          'token' => $verify->getToken(),
          'code' => $verify->getCode(),
        );

        $link = $this->getRouter()->generate('user_verify_phone', $params, true);

        $message->setTo($verify->getPhone()->getPhone());

        $message->addTextLine('thechur.ch');
        $message->addTextLine('Login Code: '.$verify->getCode());
        $message->addTextLine('');
        $message->addTextLine($link);

        return $this->getDispatcher('sms')->send($message);
    }

    /**
     * Get specificed dispatcher.
     *
     * @return object
     */
    public function getDispatcher($dispatcher)
    {
        return $this->dispatcher[$dispatcher];
    }

    /**
     * Get the Router.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}
