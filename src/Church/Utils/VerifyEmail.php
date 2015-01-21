<?php

namespace Church\Utils;

use Symfony\Component\Routing\RouterInterface;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use Symfony\Bridge\Doctrine\RegistryInterface;

use Church\Entity\EmailVerify;

class VerifyEmail {

    private $dispatcher;

    private $doctrine;

    private $router;

    public function __construct(Dispatcher $dispatcher,
                                RegistryInterface $doctrine,
                                RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    public function createVerification($email) {

    }

    public function sendVerification(EmailVerify $verify)
    {

      $message = new Message();

      $params = array(
        'token' => $verify->getToken(),
        'user_id' => $verify->getEmail()->getUser()->getID(),
      );

      // Build the Message.
      $message->addTo($verify->getEmail()->getEmail());
      $message->setSubject('Confirm Your Email');
      // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
      $text = "Please visit the following location to verify your email.\n";
      $text .= $this->getRouter()->generate('verify_email', $params, true);
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

    public function setDoctrine($doctrine)
    {
      $this->doctrine = $doctrine;
      return $this;
    }

    public function getDoctrine()
    {
      return $this->doctrine;
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
