<?php

declare(strict_types=1);

return [
    'routes' => [
        'base' => '/',
        'admin' => '/admin',
    ],
    'configs' => [
        [
            'driver' => 'php',
            'options' => [
                'path' => __DIR__.'/projects/{project}.php',
            ],
        ],
    ],
];
