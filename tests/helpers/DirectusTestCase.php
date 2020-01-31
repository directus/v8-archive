<?php

declare(strict_types=1);

namespace Directus\Tests\Helpers;

use PHPUnit\Framework\TestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
class DirectusTestCase extends TestCase
{
    /**
     * Resolves a path to a data file.
     *
     * @param string $path
     */
    protected function getDataFilePath($path): string
    {
        $path = str_replace('\\', '/', $path);
        if (strlen($path) > 0 && $path[0] == '/') {
            $path = substr($path, 1);
        }

        return __DIR__.'/../data/'.$path;
    }
}
