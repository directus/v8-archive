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

    /**
     * SSO Provider.
     */
    public function ssoProvider(string $provider): void
    {
        throw new NotImplemented();
    }

    /**
     * SSO Callback.
     */
    public function ssoCallback(string $provider): void
    {
        throw new NotImplemented();
    }
}
