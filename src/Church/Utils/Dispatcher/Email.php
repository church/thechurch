<?php

namespace Church\Utils\Dispatcher;

use Church\Message\Email as Message;

class Email
{

    // @TODO Ineject Send Grid.

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
}
