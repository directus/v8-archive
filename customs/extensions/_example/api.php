<?php

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Util\DateTimeUtils;

$app = \Directus\Application\Application::getInstance();

$app->get('/time/?', function (Request $request, Response $response) use ($app) {
    $datetime = DateTimeUtils::now();

    return $response->withJson([
        'datetime' => $datetime->toISO8601Format()
    ]);
});
