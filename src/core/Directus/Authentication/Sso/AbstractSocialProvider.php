<?php

namespace Directus\Authentication\Sso;

use Directus\Application\Container;
use Directus\Collection\Collection;

abstract class AbstractSocialProvider implements SocialProviderInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Collection
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $provider;

    /**
     * @var string
     */
    protected $token;

    /**
     * AbstractSocialProvider constructor.
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;
        $this->config = new Collection($config);

        $this->createProvider();
    }

    /**
     * Gets provider instance.
     *
     * @return mixed
     */
    public function getProvider()
    {
        if (!$this->provider) {
            $this->createProvider();
        }

        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Gets authorization token.
     *
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Gets the redirect url for the given service name.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->config->get('callback_url');
    }

    /**
     * Creates the provider oAuth client.
     *
     * @return mixed
     */
    abstract protected function createProvider();
}
