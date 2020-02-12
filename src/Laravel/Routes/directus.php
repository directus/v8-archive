<?php

/**
 * WARNING: Changing this file IS dangerous.
 *
 * To make directus load this file, make sure it's named "directus.php". We
 * export it as "_directus.php" by default to make sure you know what you're doing.
 *
 * Also note that enabling the load of this file makes it impossible to automatically
 * might cause a route mismatch between versions, so whenever you upgrade directus, you
 * should make sure that this file gets updated too.
 *
 * If there's a real need to customize directus routes, we suggest opening an issue
 * asking for it so we can see if there's a way we can extend directus to support
 * your use case.
 */

declare(strict_types=1);

use Directus\Laravel\Controllers\ProjectController;
use Directus\Laravel\Controllers\ServerController;
use Directus\Laravel\Middlewares\ProjectIdentifierMiddleware;
use Illuminate\Support\Facades\Route;

/**
 * Makes all directus routes.
 */
(function (): void {
    $base = config('directus.routes.root', '/');
    $identification = config('directus.identification');

    $rootOptions = [
        'prefix' => $base,
    ];

    $projectPrefix = '';
    if ($identification['method'] === 'path') {
        $projectPrefix = '{project}';
    } elseif ($identification['method'] === 'domain') {
        $rootOptions['domain'] = $identification['options']['pattern'];
    }

    // Directus base
    Route::group($rootOptions, function () use ($projectPrefix): void {
        // Server
        // https://docs.directus.io/api/server.html#server
        Route::group(['prefix' => 'server'], function (): void {
            Route::get('info', [ServerController::class, 'info']);
            Route::get('ping', [ServerController::class, 'ping']);
            Route::get('projects', [ServerController::class, 'projects']);
        });

        // Project
        Route::group([
            'prefix' => $projectPrefix,
            'middleware' => [
                ProjectIdentifierMiddleware::class,
            ],
        ], function (): void {
            Route::get('{collection}/items', [ProjectController::class, 'test']);
        });
    });
})();
