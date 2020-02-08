<?php

declare(strict_types=1);

namespace Directus\Core\Config\Providers\Php;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Config\Loaders\LoaderInterface;
use Directus\Core\Config\Loaders\RequireLoader;
use Directus\Core\Config\Providers\ArrayProvider;
use Directus\Core\Config\Providers\ProviderInterface;
use Directus\Core\Options\Options;

/**
 * PHP directory provider.
 */
class DirectoryProvider implements ProviderInterface
{
    /**
     * Path.
     */
    public const OPTION_PATH = 'path';

    /**
     * Loader.
     */
    public const OPTION_LOADER = 'loader';

    /**
     * Options.
     *
     * @var \Directus\Core\Options\Options
     */
    private $options;

    /**
     * Loaded projects.
     *
     * @var array
     */
    private $projects;

    /**
     * Initializes the provider.
     */
    public function __construct(array $options)
    {
        $this->options = new Options([
            static::OPTION_PATH => [
                Options::PROP_VALIDATE => function ($value): bool {
                    return strpos($value, '{project}') !== false;
                },
            ],
            static::OPTION_LOADER => [
                Options::PROP_DEFAULT => new RequireLoader(),
                Options::PROP_VALIDATE => function ($value): bool {
                    return $value instanceof LoaderInterface;
                },
            ],
        ], $options);

        $this->projects = [];
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $default = null)
    {
        [$project, $path] = $this->separate($key);

        return $this->project($project)->get($path, $default);
    }

    /**
     * Loads a project from disk.
     *
     * @param string $project
     *
     * @return ProviderInterface
     */
    public function load($project)
    {
        /** @var LoaderInterface */
        $loader = $this->options->get(static::OPTION_LOADER);

        /** @var string */
        $file = str_replace('{project}', $project, $this->options->get(static::OPTION_PATH));
        if (!$loader->exists($file)) {
            throw new FileNotFound($file);
        }

        return $this->projects[$project] = new ArrayProvider([
            ArrayProvider::OPTION_DATA => $loader->load($file),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    /*
    public function set(string $key, $value): bool
    {
        [$project, $path] = $this->separate($key);

        return $this->project($project)->set($path, $value);
    }
    */

    /**
     * Gets the project configuration.
     *
     * @param string $project
     *
     * @return ProviderInterface
     */
    private function project($project)
    {
        if (isset($this->projects[$project])) {
            return $this->projects[$project];
        }

        return $this->load($project);
    }

    /**
     * Separates the project from configuration path.
     *
     * @param string $key
     *
     * @return array
     */
    private function separate($key)
    {
        $parts = explode('.', $key, 2);
        if (\count($parts) <= 1) {
            $parts[] = '';
        }

        return $parts;
    }
}
