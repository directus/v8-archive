<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Laravel\Exceptions\NotImplemented;

/**
 * Items controller.
 */
class ItemsController extends Controller
{
    public function all(string $project, string $collection): void
    {
        throw new NotImplemented();
    }

    public function fetch(string $project, string $collection, string $id): void
    {
        throw new NotImplemented();
    }

    public function create(string $project, string $collection): void
    {
        throw new NotImplemented();
    }

    public function update(string $project, string $collection, string $id): void
    {
        throw new NotImplemented();
    }

    public function delete(string $project, string $collection, string $id): void
    {
        throw new NotImplemented();
    }

    public function revisions(string $project, string $collection, int $id, ?int $offset = null): void
    {
        throw new NotImplemented();
    }

    public function revert(string $project, string $collection, int $id, int $revision): void
    {
        throw new NotImplemented();
    }
}
