<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Core\Project;

/**
 * Server controller.
 */
class ProjectController extends Controller
{
    /**
     * Server information.
     */
    public function test(Project $project, string $collection): string
    {
        $name = $project->config()->key();

        return "project: {$name}, collection: {$collection}";
    }
}
