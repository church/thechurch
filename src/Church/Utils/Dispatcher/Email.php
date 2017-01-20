<?php

namespace Church\Utils\Dispatcher;

use Church\Message\Email as Message;

class Email
{

    /**
     * Send an Email message.
     *
     * @param Message
     *    Message Object compatible with Mandrill.
     */
    public function send(Message $message)
    {

        // Send the Message using Async.
        return $this->mandrill->send($message, '', array(), true);
    }

    /**
     * Get Mandrill Object.
     */
    public function getMandrill()
    {
        return $this->mandrill;
    }
}
