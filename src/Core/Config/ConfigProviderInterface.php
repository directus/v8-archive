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
     *
     * @param array $options
     */
    public function __construct(array $options);

    /**
     * Gets a configuration.
     *
     * @param string $context
     * @param array  $data
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * Updates a configuration.
     *
     * @param string $context
     * @param array  $data
     * @param mixed  $value
     *
     * @return bool
     */
    public function set(string $key, $value): bool;
}
