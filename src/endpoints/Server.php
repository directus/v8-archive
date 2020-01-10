<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Route;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Exception\NotInstalledException;
use Directus\Util\StringUtils;
use Directus\Services\ServerService;
use Directus\Application\Http\Middleware\TableGatewayMiddleware;
use Directus\Services\UtilsService;

class Server extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        \Directus\create_ping_route($app);
        $app->get('/projects', [$this, 'projects']);
        $app->post('/projects', \Directus\Api\Routes\ProjectsCreate::class);
        $app->delete('/projects/{name}', \Directus\Api\Routes\ProjectsDelete::class);
        $app->get('/info', [$this, 'getInfo']);

        $controller = $this;
        $app->group('/utils', function () use ($controller, $app) {
            $app->post('/hash', [$controller, 'hash']);
            $app->post('/hash/match', [$controller, 'matchHash']);
            $app->post('/random/string', [$controller, 'randomString']);
            $app->get('/2fa_secret', [$controller, 'generate2FASecret']);
        });
    }

    /**
     * Return the projects
     *
     * @return Response
     */
    public function projects(Request $request, Response $response)
    {
        // When using Directus in Docker (or any other service that relies on environment variables), always use `_` for
        // the project key
        if (getenv("DIRECTUS_USE_ENV") === "1") {
            $projectNames[] = "_";
        } else {
            $basePath = \Directus\get_app_base_path();
            $scannedDirectory = \Directus\scan_folder($basePath . '/config');

            $configFiles = $scannedDirectory;

            if (empty($configFiles)) {
                throw new NotInstalledException('This Directus instance has not been configured. Install via the Directus App (eg: /admin) or read more about configuration at: https://docs.directus.io/getting-started/installation.html#configure');
            }

            // We're re-filtering the list of projects again before returning them. This time we'll fetch out the private
            // config files. We want to filter out the disabled ones (`_`) so we can correctly return the "No projects installed"
            // warning above.
            $projectNames = [];
            foreach ($configFiles as $fileName) {
                if (!StringUtils::startsWith($fileName, 'private.')) {
                    $projectNames[] = explode('.', $fileName)[0];
                }
            }
        }

        $responseData['data'] = $projectNames;
        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * Return the current setup of server.
     *
     * @return Response
     */
    public function getInfo(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $service = new ServerService($this->container);
        $service->validateServerInfo($data);

        $basePath = $this->container->get('path_base');
        $responseData['data'] = [
            'directus' => Application::DIRECTUS_VERSION,
            'server' => [
                'type' => $_SERVER['SERVER_SOFTWARE'],
                'rewrites' => function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : null,
                'os' => PHP_OS,
                'os_version' => php_uname('v'),
            ],
            'php' => [
                'version' => phpversion(),
                'max_upload_size' => \Directus\get_max_upload_size(ServerService::INFO_SETTINGS_RUNTIME === ServerService::INFO_SETTINGS_CORE),
                'extensions' => [
                    'pdo' => defined('PDO::ATTR_DRIVER_NAME'),
                    'mysqli' => extension_loaded("mysqli"),
                    'curl' => extension_loaded("curl"),
                    'gd' => extension_loaded("gd"),
                    'fileinfo' => extension_loaded("fileinfo"),
                    'mbstring' => extension_loaded("mbstring"),
                    'json' => extension_loaded("json"),
                ],
            ],
            'permissions' => [
                'public' => substr(sprintf('%o', fileperms($basePath . "/public")), -4),
                'logs' => substr(sprintf('%o', fileperms($basePath . "/logs")), -4),
                'uploads' => substr(sprintf('%o', fileperms($basePath . "/public/uploads")), -4),
            ]
        ];
        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function hash(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $service = new UtilsService($this->container);

        $options = $request->getParsedBodyParam('options', []);
        if (!is_array($options)) {
            $options = [$options];
        }

        $responseData = $service->hashString(
            $request->getParsedBodyParam('string'),
            $request->getParsedBodyParam('hasher', 'core'),
            $options
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function matchHash(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $service = new UtilsService($this->container);

        $options = $request->getParsedBodyParam('options', []);
        if (!is_array($options)) {
            $options = [$options];
        }

        $responseData = $service->verifyHashString(
            $request->getParsedBodyParam('string'),
            $request->getParsedBodyParam('hash'),
            $request->getParsedBodyParam('hasher', 'core'),
            $options
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function randomString(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $service = new UtilsService($this->container);
        $responseData = $service->randomString(
            $request->getParsedBodyParam('length', 32),
            $request->getParsedBodyParam('options')
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /** Endpoint to generate a 2FA secret
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function generate2FASecret(Request $request, Response $response)
    {
        $service = new UtilsService($this->container);
        $responseData = $service->generate2FASecret();
        return $this->responseWithData($request, $response, $responseData);
    }
}
