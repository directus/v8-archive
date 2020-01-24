<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Laravel\Exceptions\NotImplemented;

/**
 * Collections controller.
 */
class CollectionsController extends Controller
{
    public function all(string $project): void
    {
        throw new NotImplemented();
    }

    public function fetch(string $project, string $collection): void
    {
        throw new NotImplemented();
    }

    public function create(string $project): void
    {
        throw new NotImplemented();
    }

    public function update(string $project, string $collection): void
    {
        throw new NotImplemented();
    }

    public function delete(string $project, string $collection): void
    {
        throw new NotImplemented();
    }
}
