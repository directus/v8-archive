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
    public function testContainsVersionInformation(): void
    {
        static::assertRegExp('/^\\d+.\\d+.\\d+$/', Metadata::getVersion());
    }
}
