<?php

declare(strict_types=1);

namespace Directus\Laravel\Providers;

use Directus\Core\Directus;
use Directus\Core\Project;
use Directus\Laravel\Middlewares\DirectusMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Directus provider.
 */
class DirectusProvider extends ServiceProvider
{
    /**
     * Service register.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/directus.php',
            'directus'
        );

        $this->app->singleton(Directus::class, function (): Directus {
            return new Directus([
                'config' => config('directus.config'),
            ]);
        });

        $this->app->bind(Project::class, function (): Project {
            /** @var Directus */
            $directus = resolve(Directus::class);

            return $directus->getCurrentProject();
        });

        $this->app->register(ProjectProvider::class);
        $this->app->register(AdminProvider::class);
    }

    /**
     * Service boot.
     */
    public function boot(Router $router): void
    {
        // Configs

        $this->publishes([
            __DIR__.'/../Config/directus.php' => config_path('directus.php'),
            __DIR__.'/../Config/projects/default.php' => config_path('projects/default.php'),
        ], 'directus.config');

        /*

        // Migrations

        $this->loadMigrationsFrom(__DIR__.'/../Core/Migrations');

        // TODO: better way to do route configuration
        $this->publishes([
            __DIR__.'/Routes/directus.php' => base_path('routes/_directus.php'),
        ], 'directus.routes');

        // Commands

        if ($this->app->runningInConsole()) {
            $this->commands([
                Migrate::class,
            ]);

            return;
        }
        */

        // Middlewares

        $router->middlewareGroup('directus', [
            DirectusMiddleware::class,
        ]);

        // Routes

        $routesFile = base_path('routes/directus.php');
        if (!file_exists($routesFile)) {
            $routesFile = __DIR__.'/../Routes/directus.php';
        }

        $this->loadRoutesFrom($routesFile);
    }
}
