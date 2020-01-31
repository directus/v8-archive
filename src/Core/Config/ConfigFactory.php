<?php

declare(strict_types=1);

namespace Directus\Core\Config;

use Directus\Core\Config\Exception\UnknownProvider;
use Directus\Core\Config\Providers\PhpProvider;

/**
 * Config factory.
 */
class ConfigFactory
{
    /**
     * Config driver factories.
     */
    private static $providers = [
        'php' => PhpProvider::class,
    ];

    /**
     * Registers a config driver.
     *
     * @param [type] $class
     * @param mixed  $provider
     */
    public static function register(string $name, $provider)
    {
        static::$providers[$name] = $provider;
    }

    /**
     * Gets an instance of a config loader.
     *
     * @param string $driver
     */
    public static function create(string $provider, array $options): ConfigProviderInterface
    {
        if (!\array_key_exists($provider, static::$providers)) {
            throw new UnknownProvider();
        }

        /** @var ConfigProviderInterface */
        $instance = new static::$providers[$provider]($options);

        return $instance;
    }
}
