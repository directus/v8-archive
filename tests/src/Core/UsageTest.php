<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Directus;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * @internal
 * @coversNothing
 */
final class UsageTest extends DirectusTestCase
{
    /**
     * protected $provider;
     *
     * protected function setUp(): void
     * {
     * $this->provider = new
     * }
     */
    public function testUsage1(): void
    {
        $directus = new Directus([
            'config' => [
                'provider' => 'array',
                'options' => [
                    'data' => [
                        'project1' => [
                            'database' => [
                                'driver' => 'mysql',
                                'host' => '127.0.0.1',
                            ],
                        ],
                        'project2' => [
                            'database' => [
                                'driver' => 'mysql',
                                'host' => '127.0.0.1',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $project1 = $directus->getProject('project1');
    }
}
