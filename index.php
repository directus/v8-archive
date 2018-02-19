<?php

/** @var \Directus\Application\Application $app */
$app = require __DIR__ . '/api/bootstrap.php';

// =============================================================================
// TODO: User-defined endpoints
// TODO: User-defined extensions endpoints
// TODO: Customized Method not allowed error
// =============================================================================

$app->group('/{env}', function () {
    $this->group('/activity', \Directus\Api\Routes\Activity::class);
    $this->group('/auth', \Directus\Api\Routes\Auth::class);
    $this->group('/fields', \Directus\Api\Routes\Fields::class);
    $this->group('/files', \Directus\Api\Routes\Files::class);
    $this->group('/groups', \Directus\Api\Routes\Groups::class);
    $this->group('/items', \Directus\Api\Routes\Items::class);
    $this->group('/collection_presets', \Directus\Api\Routes\CollectionPresets::class);
    $this->group('/permissions', \Directus\Api\Routes\Permissions::class);
    $this->group('/revisions', \Directus\Api\Routes\Revisions::class);
    $this->group('/settings', \Directus\Api\Routes\Settings::class);
    $this->group('/collections', \Directus\Api\Routes\Collections::class);
    $this->group('/users', \Directus\Api\Routes\Users::class);
    $this->group('/utils', \Directus\Api\Routes\Utils::class);
});

$app->run();
