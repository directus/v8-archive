<?php

/**
 * WARNING: Changing this file can be dangerous.
 * Make sure to rename it to "directus.php" to enable custom
 * directus routing.
 */

declare(strict_types=1);

use Directus\Laravel\Controllers\ActivityController;
use Directus\Laravel\Controllers\AuthController;
use Directus\Laravel\Controllers\CollectionsController;
use Directus\Laravel\Controllers\ItemsController;
use Illuminate\Support\Facades\Route;
use Directus\Laravel\Controllers\ServerController;

/**
 * Makes all directus routes.
 */
(function () {
    $base = config('directus.routes.base', '');

    // Directus base
    Route::group(['prefix' => $base], function () {
        // Server
        // https://docs.directus.io/api/server.html#server
        Route::group(['prefix' => 'server'], function () {
            Route::get('info', [ServerController::class, 'info']);
            Route::get('ping', [ServerController::class, 'ping']);
            Route::get('projects', [ServerController::class, 'projects']);
        });

        // Project
        //
        Route::group(['prefix' => '{project}'], function () {
            // Activity
            // https://docs.directus.io/api/activity.html#activity
            Route::group(['prefix' => 'activity'], function () {
                Route::get('', [ActivityController::class, 'all']);
                Route::get('{id}', [ActivityController::class, 'fetch']);
                Route::post('comment', [ActivityController::class, 'createComment']);
                Route::patch('comment/{id}', [ActivityController::class, 'updateComment']);
                Route::delete('comment/{id}', [ActivityController::class, 'deleteComment']);
            });

            // Authentication
            // https://docs.directus.io/api/authentication.html#authentication
            Route::group(['prefix' => 'auth'], function () {
                Route::post('authenticate', [AuthController::class, 'authenticate']);
                Route::post('refresh', [AuthController::class, 'refresh']);
                Route::post('password/request', [AuthController::class, 'passwordRequest']);
                Route::post('password/reset', [AuthController::class, 'passwordReset']);
                Route::get('sso', [AuthController::class, 'sso']);
                Route::get('sso/{provider}', [AuthController::class, 'ssoProvider']);
                Route::get('sso/{provider}/callback', [AuthController::class, 'ssoCallback']);
            });

            // Items
            // https://docs.directus.io/api/items.html#items
            Route::group(['prefix' => 'items/{collection}'], function () {
                Route::get('', [ItemsController::class, 'all']);
                Route::post('', [ItemsController::class, 'create']);
                Route::get('{id}', [ItemsController::class, 'fetch']);
                Route::patch('{id}', [ItemsController::class, 'update']);
                Route::delete('{id}', [ItemsController::class, 'delete']);
                Route::get('{id}/revisions/{offset?}', [ItemsController::class, 'revisions']);
                Route::patch('{id}/revert/{revision}', [ItemsController::class, 'revert']);
            });

            // Collections
            // https://docs.directus.io/api/collections.html#collections
            Route::group(['prefix' => 'collections'], function () {
                Route::get('', [CollectionsController::class, 'all']);
                Route::get('{collection}', [CollectionsController::class, 'fetch']);
                Route::post('', [CollectionsController::class, 'create']);
                Route::patch('{collection}', [CollectionsController::class, 'update']);
                Route::delete('{collection}', [CollectionsController::class, 'delete']);
            });
        });
    });
})();
