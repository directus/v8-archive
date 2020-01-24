<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Version;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class VersionTest extends TestCase
{
    /**
     * Test if version returns a semver compatible string.
     *
     * @covers \Directus\Core\Version::getVersion
     */
    public function testContainsVersionInformation(): void
    {
        static::assertRegExp('/^\\d+.\\d+.\\d+$/', Version::getVersion());
    }
}
