<?php

if (!function_exists('get_custom_endpoints')) {
    /**
     * Get the list of custom endpoints information
     *
     * @param string $path
     * @param bool $directoryOnly Ignores files in the given path
     *
     * @return array
     *
     * @throws \Directus\Exception\Exception
     */
    function get_custom_endpoints($path, $directoryOnly = false)
    {
        $endpointsPath = base_path($path);

        if (!file_exists($endpointsPath)) {
            return [];
        }

        $files = find_php_files($endpointsPath, 1);
        $endpoints = [];
        $ignoredDirectories = [];

        if ($directoryOnly) {
            $ignoredDirectories[] = '/';
        }

        foreach ($files as $file) {
            $relativePath = substr($file, strlen($endpointsPath));
            $pathInfo = pathinfo($relativePath);
            $dirname = $pathInfo['dirname'];
            $endpointName = $pathInfo['filename'];
            $isDirectory = $dirname !== '/';

            // TODO: Need to improve logic
            if (in_array($dirname, $ignoredDirectories)) {
                continue;
            }

            if ($isDirectory) {
                if ($pathInfo['filename'] === 'endpoints') {
                    $ignoredDirectories[] = $dirname;
                    $endpointName = ltrim($dirname, '/');
                } else {
                    continue;
                }
            }

            $endpointInfo = require $file;

            if (!is_array($endpointInfo)) {
                throw new \Directus\Exception\Exception('endpoint information must be an array ' . gettype($endpointInfo) . ' was given in ' . $relativePath);
            }

            $endpoints[$endpointName] = $endpointInfo;
        }

        return $endpoints;
    }
}

if (!function_exists('create_group_route_from_array')) {
    /**
     * Creates a grouped routes in the given app
     *
     * @param \Directus\Application\Application $app
     * @param string $groupName
     * @param array $endpoints
     */
    function create_group_route_from_array(\Directus\Application\Application $app, $groupName, array $endpoints)
    {
        $app->group('/' . trim($groupName, '/'), function () use ($endpoints, $app) {
            foreach ($endpoints as $routePath => $endpoint) {
                $isGroup = \Directus\Util\ArrayUtils::get($endpoint, 'group', false) === true;

                if ($isGroup) {
                    create_group_route_from_array(
                        $app,
                        $routePath,
                        (array) \Directus\Util\ArrayUtils::get($endpoint, 'endpoints', [])
                    );
                } else {
                    create_route_from_array($app, $routePath, $endpoint);
                }
            }
        });
    }
}

if (!function_exists('create_route_from_array')) {
    /**
     * Add a route to the given application
     *
     * @param \Directus\Application\Application $app
     * @param string $routePath
     * @param array $options
     *
     * @throws \Directus\Exception\Exception
     */
    function create_route_from_array(\Directus\Application\Application $app, $routePath, array $options)
    {
        $methods = \Directus\Util\ArrayUtils::get($options, 'method', ['GET']);
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        $handler = \Directus\Util\ArrayUtils::get($options, 'handler');
        if (!is_callable($handler) && !class_exists($handler)) {
            throw new \Directus\Exception\Exception(
                sprintf('Endpoints handler must be a callable, but %s was given', gettype($handler))
            );
        }

        $app->map($methods, $routePath, $handler);
    }
}
