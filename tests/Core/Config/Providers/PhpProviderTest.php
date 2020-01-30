<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Config\Providers\PhpProvider;
use Directus\Core\Options\Exception\MissingOptions;
use PHPUnit\Framework\TestCase;

/**
 * PHP Provider tests.
 */
final class PhpProviderTest extends TestCase
{
    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\PhpProvider::__construct
     */
    public function testPathShouldBeRequired(): void
    {
        $this->expectException(MissingOptions::class);

        $provider = new PhpProvider([]);
    }

    /**
     * Test the creation of php provider.
     *
     * @covers \Directus\Core\Config\Providers\PhpProvider::__construct
     */
    public function testPathShouldExists(): void
    {
        $this->expectException(MissingOptions::class);

        $provider = new PhpProvider([]);
    }
}
