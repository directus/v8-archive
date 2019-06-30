<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Context;
use Directus\Config\Schema\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $schema = Schema::get();
        $data = $schema->value([]);

        $this->assertArraySubset([
            'app' =>
            [
                'env' => 'production',
                'timezone' => 'America/New_York',
            ],
            'settings' =>
            [
                'logger' => 
                [
                ]
            ],
            'database' =>
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'name' => 'directus',
                'username' => 'root',
                'password' => 'root',
                'engine' => 'InnoDB',
                'chartset' => 'utf8mb4',
                'socket' => '',
            ],
            'cache' =>
            [
                'enabled' => false,
                'response_ttl' => 3600,
            ],
            'storage' =>
            [
                'adapter' => 'local',
                'root' => 'public/uploads/_/originals',
                'root_url' => '/uploads/_/originals',
                'thumb_root' => 'public/uploads/_/thumbnails',
            ],
            'mail' =>
            [
                'default' =>
                [
                    'transport' => 'sendmail',
                    'from' => 'admin@example.com',
                ],
            ],
            'cors' =>
            [
                'enabled' => true,
                'origin' =>
                [
                    0 => '*',
                ],
                'methods' =>
                [
                    0 => 'GET',
                    1 => 'POST',
                    2 => 'PUT',
                    3 => 'PATCH',
                    4 => 'DELETE',
                    5 => 'HEAD',
                ],
                'headers' =>
                [],
                'exposed_headers' =>
                [],
                'max_age' => NULL,
                'credentials' => false,
            ],
            'rate_limit' =>
            [
                'enabled' => false,
                'limit' => 100,
                'interval' => 60,
                'adapter' => 'redis',
                'host' => '127.0.0.1',
                'port' => 6379,
                'timeout' => 10,
            ],
            'hooks' =>
            [
                'actions' =>
                [],
                'filters' =>
                [],
            ],
            'feedback' =>
            [
                'token' => 'a-kind-of-unique-token',
                'login' => true,
            ],
            'tableblacklist' =>
            [],
            'auth' =>
            [
                'secret_key' => '<type-a-secret-authentication-key-string>',
                'public_key' => '<type-a-public-authentication-key-string>',
                'social_providers' =>
                [],
            ],
        ], $data);
    }
}
