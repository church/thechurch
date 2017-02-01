<?php

namespace Church\Entity\Message;

class SMSMessage implements MessageInterface
{

    private $to;

    private $text;

    /**
     * Set To.
     *
     * @param string $to
     *    The phone number this will be sent to in an international format.
     *
     * @return SMS
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get To.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set SMS message text.
     *
     * @param array $text
     *    Message text as an indexed array.
     *    Each array position is a line of text.
     *
     * @return SMS
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get SMS message array.
     *
     * @return array
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set SMS message line.
     *
     * @param string $text
     *    Add a Single Line of Message Text.
     *
     * @return SMS
     */
    public function addTextLine($text)
    {
        $this->text[] = $text;

        return $this;
    }

    /**
     * Get SMS message string.
     *
     * @return string
     */
    public function getTextString()
    {
        return implode("\n", $this->text);
    }
}
