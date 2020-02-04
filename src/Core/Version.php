<?php

declare(strict_types=1);

namespace Directus\Core;

/**
 * Directus version.
 */
final class Version
{
    /**
     * Gets the version set in the current VERSION file.
     */
    public static function getVersion(): string
    {
        $version = file_get_contents(Utils::getPackageDir().'/VERSION');
        if ($version === false) {
            return '0.0.0';
        }

        return trim($version);
    }
}
