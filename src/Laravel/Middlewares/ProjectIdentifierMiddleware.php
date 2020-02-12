<?php

declare(strict_types=1);

namespace Directus\Laravel\Middlewares;

use Closure;
use Directus\Core\Directus;

/**
 * Project middleware.
 */
final class ProjectIdentifierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Routing\Route */
        $route = $request->route();

        $parameters = $route->parameters();
        if (\array_key_exists('project', $parameters)) {
            /** @var Directus */
            $directus = resolve(Directus::class);
            $directus->setCurrentProject($parameters['project']);
            $route->forgetParameter('project');
        }

        return $next($request);
    }
}
