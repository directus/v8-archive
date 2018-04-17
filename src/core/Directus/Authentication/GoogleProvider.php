<?php

namespace Directus\Authentication;

use Directus\Application\Application;
use League\OAuth2\Client\Provider\Google;

class GoogleProvider extends TwoSocialProvider
{
    /**
     * @var Google
     */
    protected $provider = null;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'google';
    }

    /**
     * @inheritdoc
     */
    public function getScopes()
    {
        return [
            'email'
        ];
    }

    /**
     * Creates the Google provider oAuth client
     *
     * @return Google
     */
    protected function createProvider()
    {
        $this->provider = new Google([
            'clientId'          => $this->config->get('client_id'),
            'clientSecret'      => $this->config->get('client_secret'),
            'redirectUri'       => $this->getRedirectUrl(),
            'hostedDomain'      => $this->config->get('hosted_domain')
        ]);

        return $this->provider;
    }
}
