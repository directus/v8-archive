<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\ServerService;

class Server extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        create_ping_route($app);

        $app->get('/info', [$this, 'info']);
    }

    public function info(Request $request, Response $response)
    {
        $service = new ServerService($this->container);
        $responseData = $service->findAllInfo();
        return $this->responseWithData($request, $response, $responseData);
    }
}
