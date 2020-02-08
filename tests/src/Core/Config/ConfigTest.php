<?php

declare(strict_types=1);

namespace Directus\Tests\Core\Config;

use Directus\Core\Config\Config;
use Directus\Core\Config\Exception\UnknownProvider;
use Directus\Core\Config\Providers\ArrayProvider;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * Configuration tests.
 *
 * @coversNothing
 *
 * @internal
 */
final class ConfigTest extends DirectusTestCase
{
    /**
     * @covers \Directus\Core\Config\Config::__construct
     * @covers \Directus\Core\Config\Config::create
     */
    public function testCreateArrayProvider(): void
    {
        $config = Config::create('hello', 'array', [
            'data' => [
                'hello' => [
                    'directus' => 'awesome',
                ],
            ],
        ]);

        static::assertSame('awesome', $config->get('directus'));
    }

    /**
     * @covers \Directus\Core\Config\Config::create
     */
    public function testCreatePhpProvider(): void
    {
        $project1 = Config::create('project1', 'php_file', [
            'path' => $this->getDataFilePath('config/config.php'),
        ]);

        $project2 = Config::create('project2', 'php_file', [
            'path' => $this->getDataFilePath('config/config.php'),
        ]);

        static::assertSame('mysql', $project1->get('database.driver'));
        static::assertSame('sqlite', $project2->get('database.driver'));
    }

    /**
     * @covers \Directus\Core\Config\Config::register
     * @backupStaticAttributes enabled
     */
    public function testRegisterCustomProvider(): void
    {
        Config::register('custom', ArrayProvider::class);

        $project = Config::create('hello', 'custom', [
            'data' => [
                'hello' => [
                    'directus' => 'awesome',
                ],
            ],
        ]);

        static::assertSame('awesome', $project->get('directus'));
    }

    /**
     * @covers \Directus\Core\Config\Config::create
     */
    public function testFailOnUnknownProvider(): void
    {
        $this->expectException(UnknownProvider::class);
        Config::create('hello', 'custom', []);
    }

    /**
     * @covers \Directus\Core\Config\Config::get
     */
    public function testGetValue(): void
    {
        $config = Config::create('project_a', 'array', [
            'data' => [
                'project_a' => [
                    'key' => 'value_1',
                ],
                'project_b' => [
                    'key' => 'value_2',
                ],
                'project_c' => [
                    'key' => 'value_3',
                ],
            ],
        ]);

        static::assertSame('value_1', $config->get('key'));
    }
}
