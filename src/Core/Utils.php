<?php

declare(strict_types=1);

namespace Directus\Core;

/**
 * Directus utilities.
 */
final class Utils
{
    /**
     * Gets the root package folder.
     */
    public static function getPackageDir(): string
    {
        $path = realpath(__DIR__.'/../..');
        if (false === $path) {
            throw new \Exception('Failed to get package directory');
        }

        return $path;
    }
}
