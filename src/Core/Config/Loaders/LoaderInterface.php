<?php

declare(strict_types=1);

namespace Directus\Core\Config\Loaders;

/**
 * Loader interface.
 */
interface LoaderInterface
{
    /**
     * Check if file exists.
     */
    public function exists(string $path): bool;

    /**
     * Loads a file.
     *
     * @return mixed
     */
    public function load(string $path);
}
