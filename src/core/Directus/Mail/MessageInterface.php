<?php

namespace Directus\Mail;

interface MessageInterface
{
    /**
     * @param string $address
     * @param string|null $name
     *
     * @return void
     */
    public function setFrom($address, $name = null);

    /**
     * Gets the message from address
     *
     * @return string
     */
    public function getFrom();

    /**
     * Sets the message destinations addresses
     *
     * @param array|string $addresses
     * @param string|null $name
     *
     * @return void
     */
    public function setTo($addresses, $name = null);

    /**
     * @param array|string $addresses
     * @param string|null $name
     *
     * @return void
     */
    // public function addTo($addresses, $name = null);

    /**
     * Gets the message to addresses
     *
     * @return string
     */
    public function getTo();

    /**
     * @param array|string $addresses
     * @param string|null $name
     *
     * @return void
     */
    public function setCc($addresses, $name = null);

    /**
     * Gets the message CC
     *
     * @return string
     */
    public function getCc();

    /**
     * @param array|string $addresses
     * @param string|null $name
     *
     * @return void
     */
    public function setBcc($addresses, $name = null);

    /**
     * Gets the message BCC
     *
     * @return string
     */
    public function getBcc();

    /**
     * @param $subject
     *
     * @return void
     */
    public function setSubject($subject);

    /**
     * Gets the message subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * @param string $body
     * @param string|null $contentType
     * @param string|null $charset
     *
     * @return void
     */
    public function setBody($body, $contentType = null, $charset = null);

    /**
     * Gets the message body
     *
     * @return string
     */
    public function getBody();

    /**
     * Sets the message body content type
     *
     * @param string $type
     *
     * @return void
     */
    public function setContentType($type);

    /**
     * Gets message body content type
     *
     * @return string
     */
    public function getContentType();

    /**
     * Sets the message body charset
     *
     * @param string $charset
     *
     * @return void
     */
    public function setCharset($charset);

    /**
     * Gets the message charset
     *
     * @return string
     */
    public function getCharset();
}
