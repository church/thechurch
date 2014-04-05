<?php

namespace Church\UserBundle\Service;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use Doctrine\ORM\EntityManager;

use Church\UserBundle\Entity\EmailVerify;

class VerifyEmail {

    private $dispatcher;

    private $em;

    public function __construct(Dispatcher $dispatcher, EntityManager $em, $router) {

        $this->setDispatcher($dispatcher);
        $this->setEntityManager($em);
        $this->setRouter($router);

    }

    public function sendVerification($email) {

      $message = new Message();

      $verify = new EmailVerify();

      $url = $this->getRouter()->generate('church_user_register_email', array(), true);

      // Build the Message.
      $message->addTo($email);
      $message->setSubject('Confirm Your Email');
      // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
      $text = "Please visit the following location to verify your email.\n";
      $text .= $url.'/'.urlencode($verify->getVerification());
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
