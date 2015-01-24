<?php

namespace Church\Message;

use Hip\MandrillBundle\Message;

class Email extends Message {

    /**
     * Setup a new message with defaults
     */
    public function __construct() {
      $this->setTrackClicks(FALSE);
    }

}
