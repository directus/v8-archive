<?php

namespace Directus\Mail\Adapters;

use Directus\Collection\Collection;
use Directus\Mail\Message;

abstract class AbstractMailerAdapter implements MailerAdapterInterface
{
    /**
     * @var Collection
     */
    protected $config;

    /**
     * @var string
     */
    protected $name;

    public function __construct(array $config)
    {
        $this->config = new Collection($config);
    }

    /**
     * @return Message
     */
    public function createMessage()
    {
        return new Message();
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}
