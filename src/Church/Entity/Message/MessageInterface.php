<?php

namespace Church\Entity\Message;

use Church\Entity\EntityInterface;

/**
 * Interface for Messages.
 */
interface MessageInterface extends EntityInterface
{
    /**
     * Get To.
     */
    public function getTo() : string;

    /**
     * Get SMS message array.
     */
    public function getText() : array;

    /**
     * Get message string.
     */
    public function getTextString() : string;
}
