<?php

namespace Church\Utils\Dispatcher;

use Church\Entity\Message\MessageInterface;

/**
 * Dispatcher Interface
 */
interface DispatcherInterface
{

    /**
     * Send an Email message.
     *
     * @param MessageInterface $message
     */
    public function send(MessageInterface $message) : bool;
}
