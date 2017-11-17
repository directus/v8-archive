<?php

/** @var \Directus\Application\Application $app */
$app = require __DIR__ . '/bootstrap.php';

// =============================================================================
// TODO: User-defined endpoints
// TODO: User-defined extensions endpoints
// TODO: Customized Method not allowed
// =============================================================================

$app->group('/activities', \Directus\Api\Routes\Activities::class);
$app->group('/bookmarks', \Directus\Api\Routes\Bookmarks::class);
$app->group('/auth', \Directus\Api\Routes\Auth::class);
$app->group('/columns', \Directus\Api\Routes\Columns::class);
$app->group('/files', \Directus\Api\Routes\Files::class);
$app->group('/groups', \Directus\Api\Routes\Groups::class);
$app->group('/items', \Directus\Api\Routes\Items::class);
$app->group('/preferences', \Directus\Api\Routes\Preferences::class);
$app->group('/privileges', \Directus\Api\Routes\Privileges::class);
$app->group('/revisions', \Directus\Api\Routes\Revisions::class);
$app->group('/settings', \Directus\Api\Routes\Settings::class);
$app->group('/tables', \Directus\Api\Routes\Tables::class);
$app->group('/users', \Directus\Api\Routes\Users::class);
$app->group('/utils', \Directus\Api\Routes\Utils::class);

$app->run();
