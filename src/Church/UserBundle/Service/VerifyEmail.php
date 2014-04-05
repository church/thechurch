<?php

namespace Church\UserBundle\Service;

use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;
use Doctrine\ORM\EntityManager;

class VerifyEmail {

    private $dispatcher;

    private $em;

    public function __construct(Dispatcher $dispatcher, EntityManager $em) {

        $this->setDispatcher($dispatcher);
        $this->setEntityManager($em);

    }

    public function sendVerification($email) {

      $message = new Message();

      // Build the Message.
      $message->addTo($email);
      $message->setSubject('Confirm Your Email');
      // @TODO: Add the Validation Code to the Email (Render a Twig Template?).
      $message->setText('Click on this validation email to validate your email.');

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

}
