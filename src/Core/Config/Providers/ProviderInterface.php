<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers;

/**
 * Directus config.
 */
interface ProviderInterface
{
    /**
     * Constructs the provider.
     */
    public function __construct(array $options);

    /**
     * Gets a configuration.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Updates a configuration.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): bool;
}
