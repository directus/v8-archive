<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\Exception\FileNotFound;
use Directus\Core\Config\Providers\PhpProvider;
use Directus\Core\Options\Exception\MissingOptions;
use PHPUnit\Framework\TestCase;

/**
 * PHP Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class PhpProviderTest extends TestCase
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
            'path' => __DIR__.'/../../../fixtures/config/directus.php',
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
        $provider = $this->getProvider();
        static::assertEquals('mysql', $provider->get('project1.database.driver'));
    }
}
