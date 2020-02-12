<?php

declare(strict_types=1);

namespace Directus\Laravel\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Project provider.
 */
class ProjectProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function provides(): array
    {
        return [];
    }
}
