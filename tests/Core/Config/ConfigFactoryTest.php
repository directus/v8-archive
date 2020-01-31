<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\ConfigFactory;
use PHPUnit\Framework\TestCase;

/**
 * Configuration tests.
 *
 * @internal
 * @coversNothing
 */
final class ConfigFactoryTest extends TestCase
{
    /**
     * Test if version returns a semver compatible string.
     *
     * @covers \Directus\Core\Utils::getPackageDir
     */
    public function testPhpDriver(): void
    {
        $config = ConfigFactory::create('php', [
            'path' => __DIR__.'/../../fixtures/config/full.php',
        ]);
        static::assertSame($config->get('hello'), 'world');
    }
}
