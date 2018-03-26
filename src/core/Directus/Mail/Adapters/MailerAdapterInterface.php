<?php

namespace Directus\Mail\Adapters;

use Directus\Mail\MessageInterface;

interface MailerAdapterInterface
{
    /**
     * Creates a message object
     *
     * @return MessageInterface
     */
    public function createMessage();

    /**
     * Sends a message
     *
     * @param MessageInterface $message
     *
     * @return mixed
     */
    public function send(MessageInterface $message);

    /**
     * Sets the mailer registration name
     *
     * @param $name
     *
     * @return string
     */
    public function setName($name);

    /**
     * Gets the mailer registration name
     *
     * @return string
     */
    public function getName();
}
