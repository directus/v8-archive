<?php

if (!function_exists('create_app'))  {
    /**
     * Creates an api application
     *
     * @param string $basePath
     * @param array $config
     *
     * @return \Directus\Application\Application
     */
    function create_app($basePath, array $config)
    {
        return new \Directus\Application\Application(
            $basePath,
            $config
        );
    }
}

if (!function_exists('create_app_with_env')) {
    /**
     * Creates an api application with the given environment
     *
     * @param $basePath
     * @param $env
     *
     * @return \Directus\Application\Application
     *
     * @throws \Directus\Exception\Exception
     */
    function create_app_with_env($basePath, $env)
    {
        $configPath = $basePath . '/config';
        $configFilePath = $configPath . '/api.php';

        if (!empty($env) && $env !== '_') {
            $configFilePath = sprintf('%s/api.%s.php', $configPath, $env);
        }

        if (!file_exists($configFilePath)) {
            throw new \Directus\Exception\Exception('Unknown environment: ' . $env);
        }

        return create_app($basePath, require $configFilePath);
    }
}

if (!function_exists('ping_route')) {
    /**
     * Returns a ping route
     *
     * @param \Directus\Application\Application $app
     *
     * @return Closure
     */
    function ping_route(\Directus\Application\Application $app)
    {
        return function (\Directus\Application\Http\Request $request, \Directus\Application\Http\Response $response) {
            /** @var \Directus\Container\Container $container */
            $container = $this;
            $settings = $container->has('settings') ? $container->get('settings') : new \Directus\Collection\Collection();

            if ($settings->get('env', 'development') === 'production') {
                $response = $response->withStatus(404);
            } else {
                $body = new \Slim\Http\Body(fopen('php://temp', 'r+'));
                $body->write('pong');
                $response = $response->withBody($body);
            }

            return $response;
        };
    }
}

if (!function_exists('create_ping_route')) {
    /**
     * Create a new ping the server route
     *
     * @param $app
     *
     * @return \Directus\Application\Application
     */
    function create_ping_route(\Directus\Application\Application $app)
    {
        /**
         * Ping the server
         */
        $app->get('/ping', ping_route($app))->setName('server_ping');

        return $app;
    }
}

if (!function_exists('create_ping_server')) {
    /**
     * Creates a simple app
     *
     * @param string $basePath
     * @param array $config
     *
     * @return \Directus\Application\Application
     */
    function create_ping_server($basePath, array $config = [])
    {
        $app = create_app($basePath, array_merge([
            'app' => [
                'env' => 'production'
            ]
        ], $config));

        create_ping_route($app);

        return $app;
    }
}

if (!function_exists('ping_server')) {
    /**
     * Ping the API Server
     *
     * @return bool
     */
    function ping_server()
    {
        // @TODO: Fix error when the route exists but there's an error
        // It will not return "pong" back
        $response = @file_get_contents(get_url('/api/ping'));

        return $response === 'pong';
    }
}
