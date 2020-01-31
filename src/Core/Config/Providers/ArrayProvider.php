<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

use Directus\Core\Config\ConfigProviderInterface;
use Directus\Core\Options\Options;
use Illuminate\Support\Arr;

/**
 * Array provider.
 */
class ArrayProvider implements ConfigProviderInterface
{
    /**
     * Path.
     */
    public const OPTION_DATA = 'data';

    /**
     * Config data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Initializes the provider.
     */
    public function __construct(array $options)
    {
        $options = new Options([static::OPTION_DATA], $options);
        $this->data = $options->get(static::OPTION_DATA);
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
