<?php

namespace Directus\Authentication;

use Directus\Authentication\User\UserInterface;

interface SocialProviderInterface
{
    /**
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
