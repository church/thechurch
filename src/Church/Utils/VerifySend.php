<?php

namespace Church\Utils;

use Symfony\Component\Routing\RouterInterface;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

use Church\Entity\EmailVerify;

class VerifySend {

    private $dispatcher;

    private $router;

    public function __construct(Dispatcher $dispatcher, RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }


    public function sendEmail(EmailVerify $verify)
    {

      $message = new Message();

      $params = array(
        'type' => 'e',
        'token' => $verify->getToken(),
        'user_id' => $verify->getEmail()->getUser()->getID(),
      );

      // Build the Message.
      $message->addTo($verify->getEmail()->getEmail());
      $message->setSubject('Confirm Your Email');
      // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
      $text = "Please visit the following location to verify your email.\n";
      $text .= $this->getRouter()->generate('user_verify', $params, true);
      $message->setText($text);

      // Send the Message using Async.
      return $this->getDispatcher()->send($message, '', array(), TRUE);

    }

    public function setDispatcher($dispatcher)
    {
      $this->dispatcher = $dispatcher;

      return $this;
    }

    public function getDispatcher()
    {
      return $this->dispatcher;
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
