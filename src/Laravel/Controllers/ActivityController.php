<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Laravel\Exceptions\NotImplemented;

/**
 * Activity controller.
 */
class ActivityController extends Controller
{
    public function all(string $project): void
    {
        throw new NotImplemented();
    }

    public function fetch(string $project, int $id): void
    {
        throw new NotImplemented();
    }

    public function createComment(string $project, string $id): void
    {
        throw new NotImplemented();
    }

    public function updateComment(string $project, string $id): void
    {
        throw new NotImplemented();
    }

    public function deleteComment(string $project, string $id): void
    {
        throw new NotImplemented();
    }
}
