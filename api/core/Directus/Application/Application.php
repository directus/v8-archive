<?php

namespace Directus\Application;

use Directus\Config\Config;
use Directus\Hook\Payload;
use Directus\Util\ArrayUtils;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Exception\InvalidMethodException;

class Application extends App
{
    /**
     * Directus version
     *
     * @var string
     */
    const DIRECTUS_VERSION = '7.0.0-dev';

    /**
     * NOT USED
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * NOT USED
     *
     * @var array
     */
    protected $providers = [

    ];

    protected static $instance = null;

    /**
     * @inheritdoc
     */
    public function __construct(array $container = [])
    {

        if (is_array($container)) {
            $container = $this->createConfig($container);

            $container = new Container($container);
        }

        static::$instance = $this;

        parent::__construct($container);
    }

    /**
     * Gets the application instance (singleton)
     *
     * This is a temporary solution until we get rid of the Bootstrap object
     *
     * @return $this
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Creates the user configuration based on its configuration
     *
     * Mainly just separating the Slim settings with the Directus settings and adding paths
     *
     * @param array $appConfig
     *
     * @return array
     */
    protected function createConfig(array $appConfig)
    {
        return [
            'settings' => ArrayUtils::get($appConfig, 'settings', []),
            'config' => function () use ($appConfig) {
                return new Config($appConfig);
            },
            'api_path' => API_PATH,
            'root_path' => ROOT_PATH,
            'log_path' => LOG_PATH
        ];
    }

    /**
     * @inheritdoc
     */
    public function run($silent = false)
    {
        if (!$this->booted) {
            $this->boot();
        }

        return parent::run($silent);
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        // foreach ($this->providers as $provider) {
        //     $provider->boot($this);
        // }

        $this->booted = true;
    }

    /**
     * Get the Directus Version
     *
     * @return string
     */
    public function getVersion()
    {
        return static::DIRECTUS_VERSION;
    }

    public function response()
    {
        $response = parent::response();

        if (func_num_args() > 0) {
            $data = ArrayUtils::get(func_get_args(), 0);
            $options = ArrayUtils::get(func_get_args(), 1);

            $data = $this->triggerResponseFilter($data, (array) $options);

            // @TODO: Response will support xml
            $response->setBody(json_encode($data));
        }

        return $response;
    }

    /**
     * Trigger Filter by name with its payload
     *
     * @param $name
     * @param $payload
     *
     * @return mixed
     */
    public function triggerFilter($name, $payload)
    {
        return $this->getContainer()->get('hookEmitter')->apply($name, $payload);
    }

    /**
     * Trigger given action name
     *
     * @param $name
     * @param $params
     *
     * @return void
     */
    public function triggerAction($name, $params = [])
    {
        if (!is_array($params)) {
            $params = [$params];
        }

        array_unshift($params, $name);

        call_user_func_array([$this->getContainer()->get('hookEmitter'), 'run'], $params);
    }

    public function onMissingRequirements(Callable $callback)
    {
        $errors = get_missing_requirements();

        if ($errors) {
            $callback($errors);
            exit; // Stop
        }
    }

    /**
     * Trigger a response filter
     *
     * @param $data
     * @param array $options
     *
     * @return mixed
     */
    protected function triggerResponseFilter($data, array $options)
    {
        $uriParts = explode('/', trim($this->request()->getResourceUri(), '/'));
        $apiVersion = (float) array_shift($uriParts);

        if ($apiVersion > 1) {
            $meta = ArrayUtils::get($data, 'meta');
        } else {
            $meta = [
                'type' => array_key_exists('rows', $data) ? 'collection' : 'item',
                'table' => ArrayUtils::get($options, 'table')
            ];
        }

        $attributes = [
            'meta' => $meta,
            'apiVersion' => $apiVersion,
            'request' => [
                'path' => $this->request()->getResourceUri(),
                'method' => $this->request()->getMethod()
            ]
        ];

        $payload = new Payload($data, $attributes);

        $method = strtolower($this->request()->getMethod());
        $payload = $this->triggerFilter('response', $payload);
        $payload = $this->triggerFilter('response.' . $method, $payload);
        if ($meta['table']) {
            $payload = $this->triggerFilter('response.' . $meta['table'], $payload);
            $payload = $this->triggerFilter(sprintf('response.%s.%s',
                $meta['table'],
                $method
            ), $payload);
        }

        return $payload->getData();
    }

    protected function guessOutputFormat()
    {
        $app = $this;
        $outputFormat = 'json';
        $requestUri = $app->request->getResourceUri();

        if ($this->requestHasOutputFormat()) {
            $outputFormat = $this->getOutputFormat();
            // TODO: create a replace last/first occurrence
            $pos = strrpos($requestUri, '.' . $outputFormat);
            $newRequestUri = substr_replace($requestUri, '', $pos, strlen('.' . $outputFormat));
            $env = $app->environment();
            $env['PATH_INFO'] = $newRequestUri;
        }

        return $outputFormat;
    }

    protected function requestHasOutputFormat()
    {
        $matches = $this->getOutputFormat();

        return $matches ? true : false;
    }

    protected function getOutputFormat()
    {
        $requestUri = trim($this->request->getResourceUri(), '/');

        // @TODO: create a startsWith and endsWith using regex
        $matches = [];
        preg_match('#\.[\w]+$#', $requestUri, $matches);

        return isset($matches[0]) ? substr($matches[0], 1) : null;
    }
}
