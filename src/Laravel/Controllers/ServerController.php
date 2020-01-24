<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Laravel\Exceptions\NotImplemented;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

/**
 * Server controller.
 */
class ServerController extends Controller
{
    /**
     * Server information.
     */
    public function info(): void
    {
        throw new NotImplemented();
    }

    /**
     * Server information.
     */
    public function projects(): JsonResponse
    {
        return Response::json([
            'data' => [],
            'public' => true,
        ]);
    }

    /**
     * Server ping.
     */
    public function ping(): string
    {
        return 'pong';
    }
}
