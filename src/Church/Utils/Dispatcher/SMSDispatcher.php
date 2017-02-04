<?php

namespace Church\Utils\Dispatcher;

use NexmoMessage as Nexmo;
use Church\Entity\Message\MessageInterface;

/**
 * SMS Dispatcher
 */
class SMSDispatcher implements DispatcherInterface
{

    /**
     * @var Nexmo
     */
    protected $nexmo;

    /**
     * @var string
     */
    protected $from;

    /**
     * Send an SMS message.
     *
     * @param Nexmo $nexmo
     *    Nexmo Object from SDK.
     * @param string $from
     *    SMS From Number.
     */
    public function __construct(Nexmo $nexmo, string $from)
    {
        $this->nexmo = $nexmo;
        $this->from = $from;
    }

    /**
     * Send an SMS message.
     *
     * @param MessageInterface $message
     */
    public function send(MessageInterface $message) : bool
    {

        // Send the Message.
        $result = $this->nexmo->sendText(
            $message->getTo(),
            $this->from,
            $message->getTextString()
        );

        // Nexmo does not throw an exception when there was an error, so we'll
        // throw one ourselves.
        if (!empty($result->messages)) {
            $error = $result->messages[0];
            if (!empty($error->errortext)) {
                throw new \Exception($error->errortext);
            }
        }

        return true;
    }
}
