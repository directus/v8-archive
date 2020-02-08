<?php

declare(strict_types=1);

namespace Directus\Core\Config\Loaders;

/**
 * Load files with require.
 */
class RequireLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $path)
    {
        return require $path;
    }
}
