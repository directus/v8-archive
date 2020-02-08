<?php

declare(strict_types=1);

namespace Directus\Tests\Core\Config\Providers;

use Directus\Core\Config\Providers\ArrayProvider;
use Directus\Core\Options\Exception\MissingOptions;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * Array Provider tests.
 *
 * @internal
 * @coversNothing
 */
final class ArrayProviderTest extends DirectusTestCase
{
    /**
     * Provider.
     *
     * @var ArrayProvider
     */
    protected $provider;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->provider = new ArrayProvider([
            'data' => require $this->getDataFilePath('config/simple.php'),
        ]);
    }

    /**
     * @covers \Directus\Core\Config\Providers\ArrayProvider::__construct
     */
    public function testDataShouldBeRequired(): void
    {
        $this->expectException(MissingOptions::class);

        new ArrayProvider([]);
    }

    /**
     * @covers \Directus\Core\Config\Providers\ArrayProvider::get
     */
    public function testGetConfig(): void
    {
        static::assertSame('world', $this->provider->get('hello'));
    }
}
