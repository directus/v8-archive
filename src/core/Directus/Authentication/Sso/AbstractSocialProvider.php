<?php

namespace Directus\Authentication\Sso;

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
     * @return string
     */
    public function getRedirectUrl()
    {
        if ($this->config->has('callback_url')) {
            $url = $this->config->get('callback_url');
        } else {
            $url = get_url('/_/auth/sso/' . $this->getName(). '/callback');
        }

        return $url;
    }

    /**
     * Creates the provider oAuth client
     *
     * @return mixed
     */
    abstract protected function createProvider();
}
