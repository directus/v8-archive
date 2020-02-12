<?php

declare(strict_types=1);

return [
    'routes' => [
        'root' => '/',
        'admin' => '/admin',
    ],
    'config' => [
        'provider' => 'php',
        'options' => [
            'path' => __DIR__.'/projects/{project}.php',
        ],
    ],
    'identification' => [
        'method' => 'path',
        'options' => [
            'pattern' => '{project}.localtest.me',
        ],
    ],
];
