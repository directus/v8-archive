<?php

// define('STATUS_DELETED_NUM', 0);
// define('STATUS_ACTIVE_NUM', 1);

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
            ['Access-Control-Allow-Credentials', 'false']
        ]
    ],

    'hooks' => [],

    'filters' => [],

    'feedback' => [
        'token' => 'a-kind-of-unique-token',
        'login' => true
    ],

    // These tables will not be loaded in the directus schema
    'tableBlacklist' => [],

    'status' => [
        'deleted_value' => 0,
        'publish_value' => 1,
        'draft_value' => 2,
        'name' => 'status',
        'mapping' => [
            0 => [
                'name' => 'Deleted',
                'text_color' => '#FFFFFF',
                'background_color' => '#F44336',
                'subdued_in_listing' => true,
                'show_listing_badge' => true,
                'hidden_globally' => true,
                'hard_delete' => false,
                'published' => false,
                'sort' => 3
            ],
            1 => [
                'name' => 'Published',
                'text_color' => '#FFFFFF',
                'background_color' => '#3498DB',
                'subdued_in_listing' => false,
                'show_listing_badge' => false,
                'hidden_globally' => false,
                'hard_delete' => false,
                'published' => true,
                'sort' => 1
            ],
            2 => [
                'name' => 'Draft',
                'text_color' => '#999999',
                'background_color' => '#EEEEEE',
                'subdued_in_listing' => true,
                'show_listing_badge' => true,
                'hidden_globally' => false,
                'hard_delete' => false,
                'published' => false,
                'sort' => 2
            ]
        ]
    ],

    'auth' => [
        'secret_key' => '<secret-authentication-key>',
        'social_providers' => [
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
            // ],
            // 'twitter' => [
            //     'identifier'   => '',
            //     'secret'       => ''
            // ]
        ]
    ],

    'thumbnailer' => [
        '404imageLocation' => __DIR__ . '/../thumbnail/img-not-found.png',
        'supportedThumbnailDimensions' => [
            // width x height
            // '100x100',
            // '300x200',
            // '100x200',
        ],
        'supportedQualityTags' => [
            'poor' => 25,
            'good' => 50,
            'better' => 75,
            'best' => 100,
        ],
        'supportedActions' => [
            'contain' => [
                'options' => [
                    'resizeCanvas' => false, // http://image.intervention.io/api/resizeCanvas
                    'position' => 'center',
                    'resizeRelative' => false,
                    'canvasBackground' => 'ccc', // http://image.intervention.io/getting_started/formats
                ]
            ],
            'crop' => [
                'options' => [
                    'position' => 'center', // http://image.intervention.io/api/fit
                ]
            ],
        ]
    ],
];
