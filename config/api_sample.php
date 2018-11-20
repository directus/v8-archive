<?php

return [
    'app' => [
        'env' => 'production',
        'timezone' => 'America/New_York',
    ],

    'settings' => [
        'logger' => [
            'path' => __DIR__ . '/logs/app.log',
        ],
    ],

    'database' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'directus',
        'username' => 'root',
        'password' => 'root',
        'engine' => 'InnoDB',
        'charset' => 'utf8mb4',
        // When using unix socket to connect to the database the host attribute should be removed
        // 'socket' => '/var/lib/mysql/mysql.sock',
    ],

    'cache' => [
        'enabled' => false,
        'response_ttl' => 3600, // seconds
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

    'storage' => [
        'adapter' => 'local',
        // The storage root is the directus root directory.
        // All path are relative to the storage root when the path is not starting with a forward slash.
        // By default the uploads directory is located at the directus public root
        // An absolute path can be used as alternative.
        'root' => 'public/uploads/_/originals',
        // This is the url where all the media will be pointing to
        // here is where Directus will assume all assets will be accessed
        // Ex: (yourdomain)/uploads/_/originals
        'root_url' => '/uploads/_/originals',
        // Same as "root", but for the thumbnails
        'thumb_root' => 'public/uploads/_/thumbnails',
        //   'key'    => 's3-key',
        //   'secret' => 's3-secret',
        //   'region' => 's3-region',
        //   'version' => 's3-version',
        //   'bucket' => 's3-bucket'
    ],
    /*
    //Custom srorage
    'storage' => [
        'adapter' => 'custom',
        'root' => '/',
        //If you have on your CDN different paths for images and docs. Can be you use a little tweak.
        //Remove /img from 'root_url' and 'thumb_root' and modify your adapter response for the method write().
        //From ['fileName' => image.jpg] to ['fileName' => img/image.jpg]. Directus can handle this. But here 
        //is one disclaimer. With the tweak, you have to use 'thumbnails_hook' minimally in default form. Because
        //Thumbnailer can not handle this.
        'root_url'   => 'https://cdn.example.com/img',
        'thumb_root' => 'https://cdn.example.com/img',
        // class_adapter is required. 
        // The namespace of your Adapter which implements \League\Flysystem\Adapter\AdapterInterface.
        // This confing is accesible in Adapater in constructor.
        'class_adapter'  => \Some\Name\Adapter::class,
        // class_filesystem is optional. 
        // The namespace of your Filesystem which extends \League\Flysystem\Filesystem
        // This is needed if your storage(CDN) is changing the file name. Also need when you use the tweak.
        // You have to overload \League\Flysystem\Filesystem::write() and remove retyping to boolean and return 
        // ['fileName' => 'image.jpg']
        'class_filesystem'  => \Some\Name\Filesystem::class,
        // Domain for uploading is optional. Can be used in your Adapter.
        'upload_url'  => 'https://upload.cdn.example.com',
        // Hook for thumbnails is optional. Can be used if your thumbnails are accessible via file name(example)
        // or you just do not want use Thumbnailer or you have to when use tweak with fileName. The minimal form
        // is just return $thumbnail with no modification. If 'thumbnails_hook' is comment Directus take control and thumbnails are
        // made same as for S3.
        'thumbnails_hook' => function ($thumbnail) {
            //default
            return $thumbnail;
            //Example: https://cdn.example.com/image/123456.jpg -> https://cdn.example.com/image/123456_w200_h200.jpg
            //return [
            //    'url' => preg_replace("/(.jpg$|.png$)/", "_rw".$thumbnail['width']."_rh".$thumbnail['height']."$1", $thumbnail['url']),
            //    'relative_url' =>  preg_replace("/(.jpg$|.png$)/", "_rw".$thumbnail['width']."_rh".$thumbnail['height']."$1", $thumbnail['relative_url']),
            //    'dimension' =>  $thumbnail['dimension'],
            //    'width' => $thumbnail['width'],
            //    'height' => $thumbnail['height'],
            //];
        },
    ],
    */
    'mail' => [
        'default' => [
            'transport' => 'sendmail',
            'from' => 'admin@example.com'
        ],
    ],

    'cors' => [
        'enabled' => true,
        'origin' => ['*'],
        'methods' => [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD',
        ],
        'headers' => [],
        'exposed_headers' => [],
        'max_age' => null, // in seconds
        'credentials' => false,
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

    'hooks' => [
        'actions' => [],
        'filters' => [],
    ],

    'feedback' => [
        'token' => 'a-kind-of-unique-token',
        'login' => true
    ],

    // These tables will not be loaded in the directus schema
    'tableBlacklist' => [],

    'auth' => [
        'secret_key' => '<type-a-secret-authentication-key-string>',
        'public_key' => '<type-a-public-authentication-key-string>',
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
