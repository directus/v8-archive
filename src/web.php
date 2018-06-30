<?php

$basePath =  realpath(__DIR__ . '/../');
$configPath = $basePath . '/config';
$configFilePath = $configPath . '/api.php';

require $basePath . '/vendor/autoload.php';

// Creates a simple endpoint to test the server rewriting
// If the server responds "pong" it means the rewriting works
if (!file_exists($configFilePath)) {
    return \Directus\create_default_app($basePath);
}

// Get Environment name
$env = \Directus\get_api_env_from_request();
$requestUri = trim(\Directus\get_virtual_path(), '/');

$reservedNames = [
    'server',
    'interfaces',
    'pages',
    'listings',
    'types',
    'instance'
];

if ($requestUri && !empty($env) && $env !== '_' && !in_array($env, $reservedNames)) {
    $configFilePath = sprintf('%s/api.%s.php', $configPath, $env);
    if (!file_exists($configFilePath)) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => [
                'error' => 8,
                'message' => 'API Environment Configuration Not Found: ' . $env
            ]
        ]);
        exit;
    }
}

$app = \Directus\create_app($basePath, require $configFilePath);

// ----------------------------------------------------------------------------
//

// =============================================================================
// Error reporting
// -----------------------------------------------------------------------------
// Possible values:
//
//  'production' => error suppression
//  'development' => no error suppression
//  'staging' => no error suppression
//
// =============================================================================

$errorReporting = E_ALL;
$displayErrors = 1;
if ($app->getConfig()->get('app.env', 'development') === 'production') {
    $displayErrors = $errorReporting = 0;
}

error_reporting($errorReporting);
ini_set('display_errors', $displayErrors);

// =============================================================================
// Timezone
// =============================================================================
date_default_timezone_set($app->getConfig()->get('timezone', 'America/New_York'));

$container = $app->getContainer();

\Directus\register_global_hooks($app);
\Directus\register_extensions_hooks($app);

$app->getContainer()->get('hook_emitter')->run('application.boot', $app);

$app->add(new \Directus\Application\Http\Middleware\TableGatewayMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\IpRateLimitMiddleware($app->getContainer()))
    ->add(new RKA\Middleware\IpAddress())
    ->add(new \Directus\Application\Http\Middleware\CorsMiddleware($app->getContainer()));

$app->get('/', \Directus\Api\Routes\Home::class)
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));

$app->group('/instance', \Directus\Api\Routes\Instances::class);

$app->group('/{env}', function () {
    $this->group('/activity', \Directus\Api\Routes\Activity::class)
         ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
         ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/auth', \Directus\Api\Routes\Auth::class);
    $this->group('/fields', \Directus\Api\Routes\Fields::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/files', \Directus\Api\Routes\Files::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/items', \Directus\Api\Routes\Items::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/collection_presets', \Directus\Api\Routes\CollectionPresets::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/permissions', \Directus\Api\Routes\Permissions::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/relations', \Directus\Api\Routes\Relations::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/revisions', \Directus\Api\Routes\Revisions::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/roles', \Directus\Api\Routes\Roles::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/settings', \Directus\Api\Routes\Settings::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/collections', \Directus\Api\Routes\Collections::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/users', \Directus\Api\Routes\Users::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/scim', function () {
        $this->group('/v2', \Directus\Api\Routes\ScimTwo::class);
    })->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
      ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));;
    $this->group('/utils', \Directus\Api\Routes\Utils::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
    $this->group('/mail', \Directus\Api\Routes\Mail::class)
        ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
        ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));

    $this->group('/custom', function () {
        $endpointsList = \Directus\get_custom_endpoints('/public/custom/endpoints');

        foreach ($endpointsList as $name => $endpoints) {
            \Directus\create_group_route_from_array($this, $name, $endpoints);
        }
    });

    $this->group('/pages', function () {
        $endpointsList = \Directus\get_custom_endpoints('public/extensions/core/pages', true);

        foreach ($endpointsList as $name => $endpoints) {
            \Directus\create_group_route_from_array($this, $name, $endpoints);
        }
    })
      ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
      ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));

    $this->group('/interfaces', function () {
        $endpointsList = \Directus\get_custom_endpoints('public/extensions/core/interfaces', true);

        foreach ($endpointsList as $name => $endpoints) {
            \Directus\create_group_route_from_array($this, $name, $endpoints);
        }
    })
      ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($this->getContainer()))
      ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($this->getContainer()));
});

$app->group('/interfaces', \Directus\Api\Routes\Interfaces::class)
    ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));
$app->group('/listings', \Directus\Api\Routes\Listings::class)
    ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));
$app->group('/pages', \Directus\Api\Routes\Pages::class)
    ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));
$app->group('/server', \Directus\Api\Routes\Server::class)
    ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));
$app->group('/types', \Directus\Api\Routes\Types::class)
    ->add(new \Directus\Application\Http\Middleware\UserRateLimitMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middleware\AuthenticationMiddleware($app->getContainer()));

$app->add(new \Directus\Application\Http\Middleware\ResponseCacheMiddleware($app->getContainer()));

return $app;
