<?php

namespace Directus\Authentication;

use Directus\Application\Application;
use Directus\Application\Container;
use Directus\Config\Config;

abstract class AbstractSocialProvider implements SocialProviderInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $provider = null;

    /**
     * @var string
     */
    protected $token = null;

    /**
     * AbstractSocialProvider constructor.
     *
     * @param Container $container
     * @param array $config
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;
        $this->config = new Config($config);

        $this->createProvider();
    }

    /**
     * Gets provider instance
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
     * Gets authorization token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Gets the redirect url for the given service name
     *
     * @param $name
     *
     * @return string
     */
    public function getRedirectUrl($name)
    {
        return get_url('/_/auth/authenticate/' . $name . '/callback');
    }

    /**
     * Creates the provider oAuth client
     *
     * @return mixed
     */
    abstract protected function createProvider();
}
