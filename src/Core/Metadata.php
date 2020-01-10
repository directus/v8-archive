<?php

declare(strict_types=1);

namespace Directus\Core;

/**
 * Directus version information.
 */
final class Metadata
{
    /**
     * Gets the root package folder.
     */
    public static function getRootDir(): string
    {
        $path = realpath(__DIR__.'/../..');
        if (false === $path) {
            throw new \Exception('Failed to get package root directory');
        }

        return $path;
    }

    /**
     * Gets the version set in the current VERSION file.
     */
    public static function getVersion(): string
    {
        $version = file_get_contents(self::getRootDir().'/VERSION');
        if (false === $version) {
            return '0.0.0';
        }

        return trim($version);
    }
}
