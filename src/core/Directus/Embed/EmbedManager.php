<?php

namespace Directus\Embed;

use Directus\Embed\Provider\ProviderInterface;

class EmbedManager
{
    /**
     * List of registered provider.
     *
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * Parse a given url with all the registered providers.
     *
     * @param $url
     *
     * @throws \Exception
     *
     * @return array
     */
    public function parse($url)
    {
        foreach ($this->providers as $provider) {
            if ($provider->validateURL($url)) {
                return $provider->parse($url);
            }
        }

        throw new \Exception('No Providers registered.');
    }

    /**
     * Register a provider.
     *
     * @return ProviderInterface
     */
    public function register(ProviderInterface $provider)
    {
        if (!array_key_exists($provider->getName(), $this->providers)) {
            $this->providers[$provider->getName()] = $provider;
        }

        return $this->providers[$provider->getName()];
    }

    /**
     * Get a registered provider.
     *
     * @param $name
     *
     * @return null|ProviderInterface
     */
    public function get($name)
    {
        return array_key_exists($name, $this->providers) ? $this->providers[$name] : null;
    }

    /**
     * Get a registered provider by embed type.
     *
     * @param $type
     *
     * @return null|ProviderInterface
     */
    public function getByType($type)
    {
        preg_match('/embed\/([a-zA-Z0-9]+)/', $type, $matches);

        $name = isset($matches[1]) ? $matches[1] : null;

        return $name ? $this->get($name) : null;
    }
}
