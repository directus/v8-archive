<?php

return [
    'app' => [
        'path' => '/',
        'env' => 'development',
        'debug' => true,
        'default_language' => 'en',
        'timezone' => 'America/New_York',
    ],

    'settings' => [
        'debug' => true,
        'displayErrorDetails' => true,
        'logger' => [
            'name' => 'directus-api',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__ . '/logs/app.log',
        ],
    ],

    'database' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'directus',
        'username' => 'root',
        'password' => 'pass',
        'prefix' => '', // not used
        'engine' => 'InnoDB',
        'charset' => 'utf8mb4'
    ],

    'cache' => [
        'enabled' => false,
        'response_ttl' => 3600, // seconds
        'adapter' => 'filesystem',
        'path' => '/storage/cache',
        // 'pool' => [
        //    'adapter' => 'apc'
        // ],
        // 'pool' => [
        //    'adapter' => 'apcu'
        // ],
        // 'pool' => [
        //    'adapter' => 'filesystem',
        //    'path' => '../cache/', // relative to the api directory
        // ],
        // 'pool' => [
        //    'adapter'   => 'memcached',
        //    'host'      => 'localhost',
        //    'port'      => 11211
        // ],
        // 'pool' => [
        //    'adapter'   => 'redis',
        //    'host'      => 'localhost',
        //    'port'      => 6379
        // ],
    ],

    'filesystem' => [
        'adapter' => 'local',
        // By default media directory are located at the same level of directus root
        // To make them a level up outsite the root directory
        // use this instead
        // Ex: 'root' => realpath(ROOT_PATH.'/../storage/uploads'),
        // Note: ROOT_PATH constant doesn't end with trailing slash
        'root' => 'storage/uploads',
        // This is the url where all the media will be pointing to
        // here all assets will be (yourdomain)/storage/uploads
        // same with thumbnails (yourdomain)/storage/uploads/thumbs
        'root_url' => '/storage/uploads',
        'root_thumb_url' => '/storage/uploads/thumbs',
        //   'key'    => 's3-key',
        //   'secret' => 's3-key',
        //   'region' => 's3-region',
        //   'version' => 's3-version',
        //   'bucket' => 's3-bucket'
    ],

    // HTTP Settings
    'http' => [
        'emulate_enabled' => false,
        // can be null, or an array list of method to be emulated
        // Ex: ['PATH', 'DELETE', 'PUT']
        // 'emulate_methods' => null,
        'force_https' => false
    ],

    'mail' => [
        'transport' => 'mail',
        'from' => 'admin@admin.com'
    ],

    'cors' => [
        'enabled' => false,
        'origin' => ['*'],
        'headers' => [
            ['Access-Control-Allow-Headers', 'Authorization, Content-Type, Access-Control-Allow-Origin'],
            ['Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE'],
            ['Access-Control-Allow-Credentials', 'false']
        ]
    ],

    'rate_limit' => [
        'enabled' => false,
        'limit' => 100, // number of request
        'interval' => 60, // seconds
        'adapter' => 'redis',
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 10
    ],

    'hooks' => [],

    'filters' => [],

    'feedback' => [
        'token' => 'a-kind-of-unique-token',
        'login' => true
    ],

    // These tables will not be loaded in the directus schema
    'tableBlacklist' => [],

    'auth' => [
        'secret_key' => '<secret-authentication-key>',
        'social_providers' => [
            // 'okta' => [
            //     'client_id' => '',
            //     'client_secret' => '',
            //     'base_url' => 'https://dev-000000.oktapreview.com/oauth2/default'
            // ],
            // 'github' => [
            //     'client_id' => '',
            //     'client_secret' => ''
            // ],
            // 'facebook' => [
            //     'client_id'          => '',
            //     'client_secret'      => '',
            //     'graph_api_version'  => 'v2.8',
            // ],
            // 'google' => [
            //     'client_id'       => '',
            //     'client_secret'   => '',
            //     'hosted_domain'   => '*',
            // ],
            // 'twitter' => [
            //     'identifier'   => '',
            //     'secret'       => ''
            // ]
        ]
    ],
];
