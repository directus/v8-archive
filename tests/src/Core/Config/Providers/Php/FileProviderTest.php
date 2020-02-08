<?php

declare(strict_types=1);

namespace Directus\Tests\Core\Config\Providers\Php;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Config\Providers\Php\FileProvider;
use Directus\Core\Options\Exception\MissingOptions;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class FileProviderTest extends DirectusTestCase
{
    /**
     * Provider.
     *
     * @var FileProvider
     */
    protected $provider;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->provider = new FileProvider([
            'path' => $this->getDataFilePath('config/simple.php'),
        ]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\Php\FileProvider::__construct
     */
    public function testPathShouldBeRequired(): void
    {
        $this->expectException(MissingOptions::class);

        new FileProvider([]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\Php\FileProvider::__construct
     */
    public function testPathShouldExists(): void
    {
        $this->expectException(FileNotFound::class);

        new FileProvider([
            'path' => __DIR__.'/not_found',
        ]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\Php\FileProvider::get
     */
    public function testGetConfig(): void
    {
        static::assertSame('world', $this->provider->get('hello'));
    }
}
