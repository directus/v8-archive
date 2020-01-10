<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Metadata;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class MetadataTest extends TestCase
{
    /**
     * Test if version returns a semver compatible string.
     *
     * @covers \Metadata::getVersion
     */
    public function testContainsVersionInformation(): void
    {
        static::assertRegExp('/^\\d+.\\d+.\\d+$/', Metadata::getVersion());
    }

    /**
     * Test the root project path.
     *
     * @covers \Metadata::getRootDir
     */
    public function testRootProjectPath(): void
    {
        static::assertSame(realpath(__DIR__.'/../..'), Metadata::getRootDir());
    }
}
