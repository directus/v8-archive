<?php

namespace Directus\Laravel\Middlewares;

use Closure;

/**
 * Project middleware.
 */
final class ProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $parameters = $request->route()->parameters();

        // TODO: tenant/project identification
        if (array_key_exists('project', $parameters)) {
            // $request->route()->setParameter("project");
        }

        return $next($request);
    }
}
