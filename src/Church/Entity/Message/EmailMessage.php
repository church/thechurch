<?php

namespace Church\Entity\Message;

use Hip\MandrillBundle\Message;

class EmailMessage extends Message implements MessageInterface
{

    /**
     * Setup a new message with defaults
     */
    public function __construct()
    {
        $this->setTrackClicks(false);
    }
}
