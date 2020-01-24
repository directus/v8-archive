<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Directus\Laravel\Exceptions\NotImplemented;

/**
 * Server controller.
 */
class AuthController extends Controller
{
    public function authenticate(): void
    {
        throw new NotImplemented();
    }

    public function refresh(): void
    {
        throw new NotImplemented();
    }

    public function passwordRequest(): void
    {
        throw new NotImplemented();
    }

    public function passwordReset(): void
    {
        throw new NotImplemented();
    }

    public function sso(): void
    {
        throw new NotImplemented();
    }

    public function ssoProvider($provider): void
    {
        throw new NotImplemented();
    }

    public function ssoCallback($provider): void
    {
        throw new NotImplemented();
    }
}
