<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\ConfigFactory;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * Configuration tests.
 *
 * @internal
 * @coversNothing
 */
final class ConfigFactoryTest extends DirectusTestCase
{
    /**
     * Test if version returns a semver compatible string.
     *
     * @covers \Directus\Core\Utils::getPackageDir
     */
    public function testPhpDriver(): void
    {
        $config = ConfigFactory::create('php', [
            'path' => $this->getDataFilePath('config/config.php'),
        ]);

        static::assertSame('mysql', $config->get('project1.database.driver'));
        static::assertSame('sqlite', $config->get('project2.database.driver'));
    }
}
