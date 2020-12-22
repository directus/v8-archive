# Auth Providers

> Directus offers built-in authentication using securely hashed passwords. Alternatively, you can enable any of our Single Sign-On (SSO) services or create your own adapter for custom authentication. Directus also supports SCIM for external user management.

The auth providers supports OAuth 1.0 and 2.0. The Directus Auth provider is class that creates an OAuth client based on the phpleague [OAuth 1.0](https://github.com/thephpleague/oauth1-client) and [OAuth 2.0](https://github.com/thephpleague/oauth2-client) Client libraries.

## Files & Structure

### `Provider.php`

For OAuth 1.0 the provider class must extends from `Directus\Authentication\Sso\OneSocialProvider` and must implement a `createProvider` method that returns a `League\OAuth1\Client\Server` object. For OAuth 2.0 the provider class must extends from `Directus\Authentication\Sso\TwoSocialProvider` and implement a `createProvider` method that returns a `League\OAuth2\Client\Provider\AbstractProvider` object.

#### OAuth 1.0 Example

```php
<?php

namespace Directus\Authentication\Sso\Provider\twitter;

use Directus\Authentication\Sso\OneSocialProvider;
use League\OAuth1\Client\Server\Twitter;

class Provider extends OneSocialProvider
{
    /**
     * @var Twitter
     */
    protected $provider = null;

    /**
     * Creates the Twitter provider oAuth client
     *
     * @return Twitter
     */
    protected function createProvider()
    {
        $this->provider = new Twitter([
            'identifier'    => $this->config->get('identifier'),
            'secret'        => $this->config->get('secret'),
            'callback_uri'  => $this->getRedirectUrl(),
        ]);

        return $this->provider;
    }

    /**
     * @inheritdoc
     */
    public function getScopes()
    {
        return null;
    }
}
```

#### OAuth 2.0 Example

```php
<?php

namespace Directus\Authentication\Sso\Provider\google;

use Directus\Authentication\Sso\TwoSocialProvider;
use League\OAuth2\Client\Provider\Google;

class Provider extends TwoSocialProvider
{
    /**
     * @var Google
     */
    protected $provider = null;

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
            'hostedDomain'      => $this->config->get('hosted_domain'),
            'useOidcMode'       => (bool) $this->config->get('use_oidc_mode'),
        ]);

        return $this->provider;
    }
}

```

### `auth.php`

This file must specify what is the provider class using the `provider` key using a class resolution or whichever value that result positive when using `class_exists` function.

```php
<?php

return [
    'provider' => \Directus\Authentication\Sso\Provider\twitter\Provider::class
];
```

### `icon.svg`

Lastly, you must include an SVG file of the service provider's logo.

## SSO Flow

These are the steps taken when the Directus App attempts to log a user in using Single Sign-On:

1. The application will redirect to the API at `/auth/sso/<provider>`
2. The API redirects to the provider (e.g. GitHub/Facebook)
3. The user logs in on the providers website
4. Provider redirects back to the API
5. The API checks the authentication
6. The API redirects back to the application using the referral domain from the initial step 1 request. The API adds the data to the query string. If it's an error, it's going to be in the `?error` parameter, if it's successful it will add the Request Token in the `token` parameter.
7. The client will need to use the Request Token from Step 6 to make a `POST` request to the API to `/auth/sso/access_token` with a `request_token` as a required param to fetch the Access Token.
