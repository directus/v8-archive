<?php

declare(strict_types=1);

namespace Directus\Laravel;

use Directus\Laravel\Commands\Migrate;
use Directus\Laravel\Middlewares\ProjectMiddleware;
use Directus\Laravel\Middlewares\ResponseMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Directus laravel provider.
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * This namespace is applied to directus controller routes.
     *
     * @var string
     */
    protected $namespace = 'Directus\Laravel\Controllers';

    /**
     * Service register.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/directus.php',
            'directus',
        );
    }

    /**
     * Service boot.
     */
    public function boot(Router $router): void
    {
        // Files

        $this->publishes([
            __DIR__.'/Config/directus.php' => config_path('directus.php'),
            __DIR__.'/Config/projects/default.php' => config_path('projects/default.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Admin/Assets' => public_path(config('directus.routes.admin', '/admin')),
        ], 'public');

        // Migrations

        // $this->loadMigrationsFrom(__DIR__.'/../Core/Migrations');

        /*
        // TODO: better way to do route configuration
        $this->publishes([
            __DIR__.'/Routes/directus.php' => base_path('routes/_directus.php'),
        ], 'routes');
        */

        // Commands

        if ($this->app->runningInConsole()) {
            $this->commands([
                Migrate::class,
            ]);

            return;
        }

        // Middlewares

        $router->middlewareGroup('directus', [
            ResponseMiddleware::class,
            ProjectMiddleware::class,
        ]);

        // Routes

        $routesFile = base_path('routes/directus.php');
        if (file_exists($routesFile)) {
            include $routesFile;
        } else {
            include __DIR__.'/Routes/directus.php';
        }
    }
}
