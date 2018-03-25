<?php

namespace Directus\Authentication;

use Directus\Authentication\User\UserInterface;

interface SocialProviderInterface
{
    /**
     * Returns the authentication request url
     *
     * @return string
     */
    public function getRequestAuthorizationUrl();

    /**
     * Redirects to the authentication request url
     *
     * @return void
     */
    public function request();

    /**
     * @return UserInterface
     *
     * @throws \Exception
     */
    public function handle();

    /**
     * Gets providers name
     *
     * @return string
     */
    public function getName();
}
