<?php

declare(strict_types=1);

namespace Directus\Core\Config;

use Directus\Core\Config\Exception\UnknownProvider;
use Directus\Core\Config\Providers\ArrayProvider;
use Directus\Core\Config\Providers\Php\DirectoryProvider as PhpDirectoryProvider;
use Directus\Core\Config\Providers\Php\FileProvider as PhpFileProvider;
use Directus\Core\Config\Providers\ProviderInterface;

/**
 * Config factory.
 */
class Config
{
    /**
     * Config driver factories.
     *
     * @var array
     */
    private static $providers = [
        'php' => PhpDirectoryProvider::class,
        'php_file' => PhpFileProvider::class,
        'array' => ArrayProvider::class,
    ];

    /**
     * Project name.
     *
     * @var string
     */
    private $project;

    /**
     * Configuration provider instance.
     *
     * @var ProviderInterface
     */
    private $provider;

    /**
     * Creates a new configuration instance.
     */
    private function __construct(string $project, ProviderInterface $provider)
    {
        $this->project = $project;
        $this->provider = $provider;
    }

    /**
     * Registers a config driver.
     */
    public static function register(string $name, string $provider): void
    {
        static::$providers[$name] = $provider;
    }

    /**
     * Gets an instance of a config loader.
     */
    public static function create(string $project, string $provider, array $options): self
    {
        if (!\array_key_exists($provider, static::$providers)) {
            throw new UnknownProvider();
        }

        /** @var ProviderInterface */
        $instance = new static::$providers[$provider]($options);

        return new self($project, $instance);
    }

    /**
     * Gets a configuration from provider.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->provider->get("{$this->project}.{$key}", $default);
    }
}
