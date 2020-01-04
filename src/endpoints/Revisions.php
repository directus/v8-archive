<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\RevisionsService;

class Revisions extends Route
{
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->get('/{id}', [$this, 'read']);
    }

    /**
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findAll(
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findByIds(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }
}
