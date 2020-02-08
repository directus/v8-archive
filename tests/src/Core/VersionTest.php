<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Version;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * @internal
 * @coversNothing
 */
final class VersionTest extends DirectusTestCase
{
    /**
     * @covers \Directus\Core\Version::getVersion
     */
    public function testContainsVersionInformation(): void
    {
        static::assertRegExp('/^\\d+.\\d+.\\d+$/', Version::getVersion());
    }
}
