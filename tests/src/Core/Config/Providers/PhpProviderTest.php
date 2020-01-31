<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Config\Providers\PhpProvider;
use Directus\Core\Options\Exception\MissingOptions;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class PhpProviderTest extends DirectusTestCase
{
    /**
     * Provider.
     *
     * @var PhpProvider
     */
    protected $provider;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->provider = new PhpProvider([
            'path' => $this->getDataFilePath('config/simple.php'),
        ]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\PhpProvider::__construct
     */
    public function testPathShouldBeRequired(): void
    {
        $this->expectException(MissingOptions::class);

        new PhpProvider([]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\PhpProvider::__construct
     */
    public function testPathShouldExists(): void
    {
        $this->expectException(FileNotFound::class);

        new PhpProvider([
            'path' => __DIR__.'/not_found',
        ]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\PhpProvider::get
     */
    public function testGetConfig(): void
    {
        static::assertEquals('world', $this->provider->get('hello'));
    }
}
