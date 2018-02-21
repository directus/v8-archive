<?php

namespace Directus\Authentication;

/**
 * Provider for OAuth 1.0
 */
abstract class OneSocialProvider extends AbstractSocialProvider
{
    /**
     * @inheritDoc
     */
    public function request()
    {
        // These identify you as a client to the server.
        $temporaryCredentials = $this->provider->getTemporaryCredentials();

        // Store the credentials in the session.
        $session = $this->container->get('session');
        $session->set('oauth1cred', serialize($temporaryCredentials));

        // resource owner to the login screen on the server.
        $this->provider->authorize($temporaryCredentials);

        return;
    }

    /**
     * @inheritDoc
     */
    public function handle()
    {
        $get = $this->app->request()->get();
        if (!isset($get['oauth_token']) || !isset($get['oauth_verifier'])) {
            throw new \Exception('Invalid request');
        }

        $session = $this->container->get('session');

        // Retrieve the temporary credentials from step 2
        $temporaryCredentials = unserialize($session->get('oauth1cred'));

        // Third and final part to OAuth 1.0 authentication is to retrieve token
        // credentials (formally known as access tokens in earlier OAuth 1.0
        // specs).
        $tokenCredentials = $this->provider->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);

        $user = $this->provider->getUserDetails($tokenCredentials);

        return new SocialUser([
            'email' => $user->email
        ]);
    }
}
