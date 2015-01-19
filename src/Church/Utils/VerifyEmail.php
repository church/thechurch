<?php

namespace Church\Utils;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use Doctrine\ORM\EntityManager;

use Church\Entity\EmailVerify;

class VerifyEmail {

    private $dispatcher;

    private $em;

    public function __construct(Dispatcher $dispatcher, EntityManager $em, $router) {

        $this->setDispatcher($dispatcher);
        $this->setEntityManager($em);
        $this->setRouter($router);

    }

    public function sendVerification($verify) {

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

    public function setDispatcher($dispatcher) {
      $this->dispatcher = $dispatcher;

      return $this;
    }

    public function getDispatcher() {
      return $this->dispatcher;
    }

    public function setEntityManager($em) {
      $this->em = $em;

      return $this;
    }

    public function getEntityManager() {
      return $this->em;
    }

    public function setRouter($router) {
      $this->router = $router;

      return $this;
    }

    public function getRouter() {
      return $this->router;
    }

}
