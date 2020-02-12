<?php

declare(strict_types=1);

namespace Directus\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Admin provider.
 */
class AdminProvider extends ServiceProvider
{
    /**
     * Service boot.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../Admin/Assets' => public_path(config('directus.routes.admin', '/admin')),
        ], 'directus.admin');
    }
}
