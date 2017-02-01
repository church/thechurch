<?php

namespace Church\Utils\Dispatcher;

use Church\Message\Email as Message;

class EmailDispatcher implements DispatcherInterface
{

    // @TODO Ineject Send Grid.

    /**
     * Send an Email message.
     *
     * @param Message
     *    Message Object compatible with Mandrill.
     */
    public function send(Message $message) : boolean
    {

        // Send the Message using Async.
        $this->mandrill->send($message, '', array(), true);

        return true;
    }
}
