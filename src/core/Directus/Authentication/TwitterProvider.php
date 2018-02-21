<?php

namespace Directus\Authentication;

use League\OAuth1\Client\Server\Twitter;

class TwitterProvider extends OneSocialProvider
{
    /**
     * @var Twitter
     */
    protected $provider = null;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'twitter';
    }

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
            'callback_uri'  => $this->getRedirectUrl($this->getName()),
        ]);

        return $this->provider;
    }
}
