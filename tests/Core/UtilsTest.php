<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UtilsTest extends TestCase
{
    /**
     * Test if version returns a semver compatible string.
     *
     * @covers \Directus\Core\Utils::getPackageDir
     */
    public function testDirectoryIsNotEmpty(): void
    {
        $dir = Utils::getPackageDir();
        static::assertNotEmpty($dir, 'Should not be empty');
    }
}
