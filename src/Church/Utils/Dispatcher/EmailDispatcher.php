<?php

namespace Church\Utils\Dispatcher;

use Church\Entity\Message\EmailMessage;
use Church\Entity\Message\MessageInterface;
use SendGrid\Content;
use SendGrid\Mail;
use SendGrid\Email;

/**
 * Email Dispatcher
 */
class EmailDispatcher implements DispatcherInterface
{

    /**
     * @var bool
     */
    protected $dispatch;

    /**
     * @var \SendGrid
     */
    protected $sendGrid;

    /**
     * Creates an email dispatcher.
     *
     * @param bool $dispatch
     * @param \SendGrid $sendGrid
     */
    public function __construct(bool $dispatch, \SendGrid $sendGrid)
    {
        $this->dispatch = $dispatch;
        $this->sendGrid = $sendGrid;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message) : bool
    {
        if (!$this->dispatch) {
            return false;
        }

        $mail = $this->convertMessage($message);

        $response = $this->sendGrid->client->mail()->send()->post($mail);

        return $response->statusCode() == 202;
    }

    /**
     * Convert Message Entity to SendGrid Mail.
     *
     * @param EmailMessage $message
     */
    protected function convertMessage(EmailMessage $message) : Mail
    {
        $from = new Email(null, "church@thechur.ch");
        $subject = "Hello World from the SendGrid PHP Library!";
        $to = new Email(null, $message->getTo());
        $content = new Content("text/plain", $message->getTextString());

        return new Mail($from, $message->getSubject(), $to, $content);
    }
}
