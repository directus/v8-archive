<?php

declare(strict_types=1);

namespace Directus\Core\Config;

/**
 * Config interface.
 */
interface ConfigInterface
{
    /**
     * Gets a configuration from provider.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Gets the project key.
     */
    public function key(): string;
}
