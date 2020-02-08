<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Utils;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * @internal
 * @coversNothing
 */
final class UtilsTest extends DirectusTestCase
{
    /**
     * @covers \Directus\Core\Utils::getPackageDir
     */
    public function testDirectoryIsNotEmpty(): void
    {
        $dir = Utils::getPackageDir();
        static::assertNotEmpty($dir, 'Should not be empty');
    }
}
