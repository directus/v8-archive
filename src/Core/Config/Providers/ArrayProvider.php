<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

use Directus\Core\Config\ConfigProviderInterface;
use Illuminate\Support\Arr;

/**
 * Array provider.
 */
class ArrayProvider implements ConfigProviderInterface
{
    /**
     * Config data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Initializes the provider.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
