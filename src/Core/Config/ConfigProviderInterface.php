<?php

declare(strict_types=1);

namespace Directus\Core\Config;

/**
 * Directus config.
 */
interface ConfigProviderInterface
{
    /**
     * Constructs the provider.
     */
    public function __construct(array $options);

    /**
     * Gets a configuration.
     *
     * @param string $context
     * @param array  $data
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Updates a configuration.
     *
     * @param string $context
     * @param array  $data
     * @param mixed  $value
     */
    public function set(string $key, $value): bool;
}
