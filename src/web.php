<?php

/** @var \Directus\Application\Application $app */
$app = require __DIR__ . '/bootstrap.php';

$app->add(new \Directus\Application\Http\Middlewares\AuthenticationMiddleware($app->getContainer()))
    ->add(new \Directus\Application\Http\Middlewares\CorsMiddleware($app->getContainer()))
    ->add(new RKA\Middleware\IpAddress());

$app->get('/', \Directus\Api\Routes\Home::class);

$app->group('/{env}', function () {
    $this->group('/activity', \Directus\Api\Routes\Activity::class);
    $this->group('/auth', \Directus\Api\Routes\Auth::class);
    $this->group('/fields', \Directus\Api\Routes\Fields::class);
    $this->group('/files', \Directus\Api\Routes\Files::class);
    $this->group('/items', \Directus\Api\Routes\Items::class);
    $this->group('/collection_presets', \Directus\Api\Routes\CollectionPresets::class);
    $this->group('/permissions', \Directus\Api\Routes\Permissions::class);
    $this->group('/relations', \Directus\Api\Routes\Relations::class);
    $this->group('/revisions', \Directus\Api\Routes\Revisions::class);
    $this->group('/roles', \Directus\Api\Routes\Roles::class);
    $this->group('/settings', \Directus\Api\Routes\Settings::class);
    $this->group('/collections', \Directus\Api\Routes\Collections::class);
    $this->group('/users', \Directus\Api\Routes\Users::class);
    $this->group('/utils', \Directus\Api\Routes\Utils::class);

    $this->group('/custom', function () {
        $endpointsList = get_custom_endpoints('/public/custom/endpoints');

        foreach ($endpointsList as $name => $endpoints) {
            create_group_route_from_array($this, $name, $endpoints);
        }
    });

    $this->group('/pages', function () {
        $endpointsList = get_custom_endpoints('public/extensions/core/pages', true);

        foreach ($endpointsList as $name => $endpoints) {
            create_group_route_from_array($this, $name, $endpoints);
        }
    });

    $this->group('/interfaces', function () {
        $endpointsList = get_custom_endpoints('public/extensions/core/interfaces', true);

        foreach ($endpointsList as $name => $endpoints) {
            create_group_route_from_array($this, $name, $endpoints);
        }
    });
});

$app->group('/interfaces', \Directus\Api\Routes\Interfaces::class);
$app->group('/listings', \Directus\Api\Routes\Listings::class);
$app->group('/pages', \Directus\Api\Routes\Pages::class);
$app->group('/server', \Directus\Api\Routes\Server::class);
$app->group('/types', \Directus\Api\Routes\Types::class)
    ->add(new \Directus\Application\Http\Middlewares\AuthenticatedMiddleware($app->getContainer()));

// $app->add(new \Directus\Slim\HttpCacheMiddleware());
// $app->add(new \Directus\Slim\ResponseCacheMiddleware());
//
// $app->hookEmitter->run('application.boot', $app);
// // $app->hook('slim.before.dispatch', function () use ($app, $authRouteWhitelist, $ZendDb, $acl, $authentication) {
// //     // API/Server is about to initialize
// //     $app->hookEmitter->run('application.init', $app);
// //     {
// //         // User is authenticated
// //         // And Directus is about to start
// //         $app->hookEmitter->run('directus.start', $app);
// //     }
// // });
//
// $app->hook('slim.after', function () use ($app) {
//     // API/Server is about to shutdown
//     $app->hookEmitter->run('application.shutdown', $app);
// });
//
// $app->notFound(function () use ($app, $acl) {
//     $projectInfo = get_project_info();
//
//     $app->response()->header('Content-Type', 'text/html; charset=utf-8');
//     $app->render('errors/404.twig', $projectInfo);
// });

return $app;
