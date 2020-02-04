<?php

declare(strict_types=1);

namespace Directus\Core\Config;

use Directus\Core\Config\Exception\UnknownProvider;
use Directus\Core\Config\Providers\ArrayProvider;
use Directus\Core\Config\Providers\PhpProvider;
use Directus\Core\Config\Providers\ProviderInterface;

/**
 * Config factory.
 */
class ConfigFactory
{
    /**
     * Config driver factories.
     *
     * @var array
     */
    private static $providers = [
        'php' => PhpProvider::class,
        'array' => ArrayProvider::class,
    ];

    /**
     * Registers a config driver.
     */
    public static function register(string $name, ProviderInterface $provider): void
    {
        static::$providers[$name] = $provider;
    }

    /**
     * Gets an instance of a config loader.
     */
    public static function create(string $name, array $options): ProviderInterface
    {
        if (!\array_key_exists($name, static::$providers)) {
            throw new UnknownProvider();
        }

        /** @var ProviderInterface */
        $instance = new static::$providers[$name]($options);

        return $instance;
    }
}
