<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

use Directus\Core\Config\ConfigProviderInterface;
use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Options\Options;
use Illuminate\Support\Arr;

/**
 * PHP loader.
 */
class PhpProvider implements ConfigProviderInterface
{
    /**
     * Path.
     */
    public const OPTION_PATH = 'path';

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
    private $options;

    /**
     * Initializes the loader.
     */
    public function __construct(array $options)
    {
        $this->options = new Options([static::OPTION_PATH], $options);

        $file = $this->options->get(static::OPTION_PATH);
        if (!file_exists($file)) {
            throw new FileNotFound($file);
        }

        $this->data = require $file;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value): bool
    {
        $this->data = Arr::set($this->data, $key, $value);

        return true;
    }
}
