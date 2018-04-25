<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\RevisionsService;

class Revisions extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->get('/{id:[0-9]+}', [$this, 'read']);
        $app->get('/{collection}', [$this, 'allFromCollection']);
        $app->get('/{collection}/{id}', [$this, 'readFromCollection']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
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
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findOne(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function allFromCollection(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findByCollection(
            $request->getAttribute('collection'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function readFromCollection(Request $request, Response $response)
    {
        $service = new RevisionsService($this->container);
        $responseData = $service->findOneByItem(
            $request->getAttribute('collection'),
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }
}
