<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\InstallService;

class Install extends Route
{
    public function __invoke(Application $app)
    {
        $app->post('', [$this, 'install']);
    }

    public function install(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $installService = new InstallService($this->container);

        $payload = $request->getParsedBody();
        $installService->install($payload);

        return $this->responseWithData($request, $response, []);
    }
}
