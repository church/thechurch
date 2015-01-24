<?php

namespace Church\Utils;

use Symfony\Component\Routing\RouterInterface as Router;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher as Mandrill;
use NexmoMessage as Nexmo;

use Church\Entity\User\EmailVerify;
use Church\Entity\User\PhoneVerify;
use Church\Message\Email as EmailMessage;

class VerifySend {

    private $mandrill;

    private $nexmo;

    private $router;

    public function __construct(Mandrill $mandrill,
                                Nexmo $nexmo,
                                Router $router)
    {
        $this->mandrill = $mandrill;
        $this->nexmo = $nexmo;
        $this->router = $router;
    }


    public function sendEmail(EmailVerify $verify)
    {

      $message = new EmailMessage();

      $params = array(
        'token' => $verify->getToken(),
        'user_id' => $verify->getEmail()->getUser()->getID(),
      );

      // Build the Message.
      $message->addTo($verify->getEmail()->getEmail());
      $message->setSubject('Confirm Your Email');

      // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
      $text = "Please visit the following location to verify your email.\n";
      $text .= $this->getRouter()->generate('user_verify_email', $params, TRUE);

      $message->setText($text);

      // Send the Message using Async.
      return $this->getMandrill()->send($message, '', array(), TRUE);

    }

    public function sendSMS(PhoneVerify $verify)
    {

      $params = array(
        'token' => $verify->getToken(),
        'user_id' => $verify->getPhone()->getUser()->getID(),
      );

      $link = $this->getRouter()->generate('user_verify_email', $params, TRUE);

      $message = array(
        'Login Code: '.$verify->getToken(),
        $link,
      );

      $message = implode("\n", $message);

      $to = $verify->getPhone()->getPhone();
      $from = 'thechur.ch';

      return $this->getNexmo()->sendText($to, $from, $message);

    }

    public function setMandrill($mandrill)
    {
      $this->mandrill = $mandrill;

      return $this;
    }

    public function getMandrill()
    {
      return $this->mandrill;
    }

    public function setNexmo($nexmo)
    {
      $this->nexmo = $nexmo;

      return $this;
    }

    public function getNexmo()
    {
      return $this->nexmo;
    }

    public function setRouter($router)
    {
      $this->router = $router;
      return $this;
    }

    public function getRouter()
    {
      return $this->router;
    }

}
