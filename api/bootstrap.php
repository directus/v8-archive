<?php

// Composer Autoloader
$loader = require __DIR__ . '/../vendor/autoload.php';

// Constants
define('API_PATH', __DIR__);
define('ROOT_PATH', realpath(API_PATH . '/../'));
define('LOG_PATH', API_PATH . '/logs');

// TODO: REMOVE THIS, IT'S TEMPORARY
// BASE_PATH will be base path of directus relative to the host
define('BASE_PATH', ROOT_PATH);

$configFilePath = API_PATH . '/config.php';

// Creates a simple endpoint to test the server rewriting
// If the server responds "pong" it means the rewriting works
if (!file_exists($configFilePath)) {
    return create_ping_server();
}

$appConfig = require $configFilePath;

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
if (\Directus\Util\ArrayUtils::get($appConfig, 'env', 'development') === 'production') {
    $displayErrors = $errorReporting = 0;
}

error_reporting($errorReporting);
ini_set('display_errors', $displayErrors);

// =============================================================================
// Timezone
// =============================================================================
date_default_timezone_set(\Directus\Util\ArrayUtils::get($appConfig, 'timezone', 'America/New_York'));

$app = new \Directus\Application\Application($appConfig);

$container = $app->getContainer();

// =============================================================================
// Load registered hooks
// =============================================================================
$config = $container->get('config');
if ($config->has('hooks')) {
    load_registered_hooks($config->get('hooks'), false);
}

if ($config->get('filters')) {
    // set seconds parameter "true" to add as filters
    load_registered_hooks($config->get('filters'), true);
}

$app->getContainer()->get('hook_emitter')->run('application.boot', $app);

// TODO: Implement old Slim 2 hooks into middlewares

$app->add(new RKA\Middleware\IpAddress());
$app->add(new \Directus\Application\Http\Middlewares\AuthenticationMiddleware($app->getContainer()));

return $app;
