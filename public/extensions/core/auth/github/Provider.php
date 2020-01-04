<?php

namespace Directus\Authentication\Sso\Provider\github;

use Directus\Authentication\Sso\TwoSocialProvider;
use Directus\Util\ArrayUtils;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Token\AccessToken;

class Provider extends TwoSocialProvider
{
    /**
     * @var Github
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return [
            'user:email',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getResourceOwnerEmail(AccessToken $token)
    {
        $provider = $this->provider;
        $ownerEmail = null;
        $visible = [];
        $primary = null;

        $url = $this->getResourceOwnerEmailUrl($token);
        $request = $provider->getAuthenticatedRequest($provider::METHOD_GET, $url, $token);
        $response = $provider->getParsedResponse($request);

        // Remove non-verified emails
        $response = array_filter($response, function ($item) {
            return true === ArrayUtils::get($item, 'verified');
        });

        if (\is_array($response) && \count($response) > 0) {
            // fallback to the first email on the list
            $ownerEmail = $response[0]['email'];

            foreach ($response as $emailData) {
                $email = ArrayUtils::get($emailData, 'email');

                if (ArrayUtils::get($emailData, 'primary', false)) {
                    $primary = $email;
                }

                if ('public' === ArrayUtils::get($emailData, 'visibility')) {
                    $visible[] = $email;
                }
            }
        }

        // First try: pick primary email if it's visible
        // Second try: pick the first visible email
        // Third try: pick the primary email if exists
        // Fourth try: pick the first email on the list
        // Fifth try: fallback to null
        if (\in_array($primary, $visible, true)) {
            $ownerEmail = $primary;
        } elseif (\count($visible) > 0) {
            $ownerEmail = array_shift($visible);
        } elseif ($primary) {
            $ownerEmail = $primary;
        }

        return $ownerEmail;
    }

    /**
     * Gets the resource owner email url.
     *
     * @return string
     */
    protected function getResourceOwnerEmailUrl(AccessToken $token)
    {
        if ('https://github.com' === $this->provider->domain) {
            $url = $this->provider->apiDomain.'/user/emails';
        } else {
            $url = $this->provider->domain.'/api/v3/user/emails';
        }

        return $url;
    }

    /**
     * Creates the GitHub provider oAuth client.
     *
     * @return Github
     */
    protected function createProvider()
    {
        $this->provider = new Github([
            'clientId' => $this->config->get('client_id'),
            'clientSecret' => $this->config->get('client_secret'),
            'redirectUri' => $this->getRedirectUrl(),
        ]);

        return $this->provider;
    }
}
