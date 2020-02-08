<?php

declare(strict_types=1);

namespace Directus\Tests\Core\Config\Loaders;

use Directus\Core\Config\Loaders\RequireLoader;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class RequireLoaderTest extends DirectusTestCase
{
    /**
     * Test file.
     */
    private const TEST_FILE = 'config/simple.php';

    /**
     * Provider.
     *
     * @var RequireLoader
     */
    protected $loader;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->loader = new RequireLoader();
    }

    /**
     * @covers \Directus\Core\Config\Loaders\RequireLoader::exists
     */
    public function testFileExists(): void
    {
        static::assertTrue($this->loader->exists($this->getDataFilePath(static::TEST_FILE)));
    }

    /**
     * @covers \Directus\Core\Config\Loaders\RequireLoader::exists
     */
    public function testFileDoesnExists(): void
    {
        static::assertFalse($this->loader->exists(__DIR__.'/non_existent_file'));
    }

    /**
     * @covers \Directus\Core\Config\Loaders\RequireLoader::load
     */
    public function testLoadsTargetFile(): void
    {
        $data = $this->loader->load($this->getDataFilePath(static::TEST_FILE));
        static::assertSame([
            'hello' => 'world',
        ], $data);
    }
}
