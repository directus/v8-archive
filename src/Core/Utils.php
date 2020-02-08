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
        // @var string
        return (string) realpath(__DIR__.'/../..');
    }
}
