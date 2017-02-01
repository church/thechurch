<?php

namespace Church\Utils\Dispatcher;

use Church\Entity\Message\MessageInterface;

interface DispatcherInterface
{

    /**
     * Send an Email message.
     *
     * @param Message
     *    Message Object compatible with Mandrill.
     */
    public function send(MessageInterface $message) : boolean;
}
