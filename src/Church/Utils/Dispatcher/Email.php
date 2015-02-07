<?php

namespace Church\Utils\Dispatcher;

use Hip\MandrillBundle\Dispatcher as Mandrill;
use Church\Message\Email as Message;

class Email {

    private $mandrill;

    public function __construct(Mandrill $mandrill)
    {
        $this->mandrill = $mandrill;
    }

    /**
     * Send an Email message.
     *
     * @param Message
     *    Message Object compatible with Mandrill.
     */
    public function send(Message $message)
    {

      // Send the Message using Async.
      return $this->mandrill->send($message, '', array(), TRUE);

    }

    /**
     * Get Mandrill Object.
     */
    public function getMandrill()
    {
      return $this->mandrill;
    }

}
