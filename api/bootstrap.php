<?php

// Composer Autoloader
$loader = require __DIR__ . '/../vendor/autoload.php';

/** @var \Directus\Application\Application $app */
$app = require __DIR__ . '/bootstrap/application.php';

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
if ($app->getConfig()->get('env', 'development') === 'production') {
    $displayErrors = $errorReporting = 0;
}

error_reporting($errorReporting);
ini_set('display_errors', $displayErrors);

// =============================================================================
// Timezone
// =============================================================================
date_default_timezone_set($app->getConfig()->get('timezone', 'America/New_York'));

$container = $app->getContainer();

// =============================================================================
// Load registered hooks
// =============================================================================
load_registered_hooks($app);

$app->getContainer()->get('hook_emitter')->run('application.boot', $app);

// TODO: Implement old Slim 2 hooks into middlewares

$app->add(new RKA\Middleware\IpAddress());
$app->add(new \Directus\Application\Http\Middlewares\AuthenticationMiddleware($app->getContainer()));

return $app;
