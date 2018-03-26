<?php

namespace Directus\Mail;

use Directus\Exception\RuntimeException;
use Directus\Mail\Adapters\AbstractMailerAdapter;

class MailerManager
{
    /**
     * @var AbstractMailerAdapter[]
     */
    protected $adapters = [];
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Register a mailer with a name
     *
     * @param string $name
     * @param AbstractMailerAdapter $adapter
     */
    public function register($name, AbstractMailerAdapter $adapter)
    {
        if (array_key_exists($name, $this->adapters)) {
            throw new RuntimeException(sprintf('Mailer adapter named "%s" already exists.', $name));
        }

        $adapter->setName($name);
        $this->adapters[$name] = $adapter;
    }

    /**
     * @param $name
     *
     * @return AbstractMailerAdapter
     */
    public function get($name)
    {
        if (!array_key_exists($name, $this->adapters)) {
            throw new RuntimeException(sprintf('Mailer adapter named "%s" not found', $name));
        }

        return $this->adapters[$name];
    }

    /**
     * Gets the first or "default" adapter
     *
     * @return AbstractMailerAdapter|null
     */
    public function getDefault()
    {
        $adapter = null;
        if (array_key_exists('default', $this->adapters)) {
            $adapter = $this->adapters['default'];
        } else if (count($this->adapters) > 0) {
            $adapter = reset($this->adapters);
        }

        return $adapter;
    }
}
