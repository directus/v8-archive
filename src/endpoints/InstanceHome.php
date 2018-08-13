<?php

namespace Directus\Api\Routes;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\InstanceService;

class InstanceHome extends Route
{
    public function __invoke(Request $request, Response $response)
    {
        $service = new InstanceService($this->container);
        $responseData = $service->findAllInfo();

        return $this->responseWithData($request, $response, $responseData);
    }
}
