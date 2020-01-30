<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

use Directus\Core\Config\ConfigProviderInterface;
use Directus\Core\Options\Options;

/**
 * PHP loader.
 */
class PhpProvider implements ConfigProviderInterface
{
    /**
     * Config data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Options.
     *
     * @var \Directus\Core\Options\Options
     */
    private $options = null;

    /**
     * Initializes the loader.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = new Options(['path'], $options);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): bool
    {
        return false;
    }
}
