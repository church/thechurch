<?php

namespace Church\Utils\Dispatcher;

use NexmoMessage as Nexmo;
use Church\Message\SMS as Message;

class SMS
{

    private $nexmo;

    private $from;

    /**
     * Send an SMS message.
     *
     * @param Nexmo $nexmo
     *    Nexmo Object from SDK.
     * @param string $from
     *    SMS From Number.
     */
    public function __construct(Nexmo $nexmo, $from)
    {
        $this->nexmo = $nexmo;
        $this->from = $from;
    }

    /**
     * Send an SMS message.
     *
     * @param Message
     *    Message Object compatible this object.
     */
    public function send(Message $message)
    {

        // Send the Message.
        $result = $this->getNexmo()->sendText(
            $message->getTo(),
            $this->getFrom(),
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
    }

    /**
     * Get Mandrill Object.
     */
    public function getNexmo()
    {
        return $this->nexmo;
    }

    /**
     * Get From Number.
     */
    public function getFrom()
    {
        return $this->from;
    }
}
