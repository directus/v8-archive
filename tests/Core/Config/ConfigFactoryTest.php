<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\ConfigFactory;
use PHPUnit\Framework\TestCase;

/**
 * Configuration tests.
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
        ConfigFactory::create('php', [
            'path' => __DIR__.'/../../fixtures/file.php',
        ]);
    }
}
