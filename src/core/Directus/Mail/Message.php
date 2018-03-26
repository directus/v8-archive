<?php

namespace Directus\Mail;

use Directus\Collection\Collection;

class Message implements MessageInterface
{
    /**
     * @var
     */
    protected $attributes;

    public function __construct()
    {
        $this->attributes = new Collection();
    }

    /**
     * @inheritDoc
     */
    public function setFrom($address, $name = null)
    {
        if (!is_array($address) && isset($name)) {
            $address = [$address => $name];
        }

        $this->attributes->set('from', $address);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFrom()
    {
        return $this->attributes->get('from');
    }

    /**
     * @inheritDoc
     */
    public function setTo($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = [$addresses => $name];
        }

        $this->attributes->set('to', (array) $addresses);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTo()
    {
        return $this->attributes->get('to');
    }

    /**
     * @inheritDoc
     */
    public function setCc($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = [$addresses => $name];
        }

        $this->attributes->set('cc', (array) $addresses);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCc()
    {
        return $this->attributes->get('cc');
    }

    /**
     * @inheritDoc
     */
    public function setBcc($addresses, $name = null)
    {
        if (!is_array($addresses) && isset($name)) {
            $addresses = [$addresses => $name];
        }

        $this->attributes->set('bcc', (array) $addresses);
    }

    /**
     * @inheritDoc
     */
    public function getBcc()
    {
        return $this->attributes->get('bcc');
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject)
    {
        $this->attributes->set('subject', $subject);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->attributes->get('subject');
    }

    /**
     * @inheritDoc
     */
    public function setBody($body, $contentType = null, $charset = null)
    {
        $this->attributes->set('body', $body);

        $this->setContentType($contentType);
        $this->setCharset($charset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->attributes->get('body');
    }

    /**
     * @inheritDoc
     */
    public function setContentType($type)
    {
        $this->attributes->set('content_type', $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContentType()
    {
        return $this->attributes->get('content_type');
    }

    /**
     * @inheritDoc
     */
    public function setCharset($charset)
    {
        $this->attributes->set('charset', $charset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCharset()
    {
        return $this->attributes->get('charset');
    }

}
