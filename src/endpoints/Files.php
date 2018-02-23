<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\FilesServices;
use Directus\Util\ArrayUtils;

class Files extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('', [$this, 'create']);
        $app->get('/{id:[0-9]+}', [$this, 'read']);
        $app->patch('/{id:[0-9]+}', [$this, 'update']);
        $app->delete('/{id:[0-9]+}', [$this, 'delete']);
        $app->get('', [$this, 'all']);

        // Folders
        $controller = $this;
        $app->group('/folders', function () use ($controller) {
            $this->post('', [$controller, 'createFolder']);
            $this->get('/{id:[0-9]+}', [$controller, 'readFolder']);
            $this->patch('/{id:[0-9]+}', [$controller, 'updateFolder']);
            $this->delete('/{id:[0-9]+}', [$controller, 'deleteFolder']);
            $this->get('', [$controller, 'allFolder']);
        });
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->create(
            $request->getParsedBody(),
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
        $service = new FilesServices($this->container);
        $responseData = $service->find(
            $request->getAttribute('id'),
            ArrayUtils::pick($request->getParams(), ['fields', 'meta'])
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->update(
            $request->getAttribute('id'),
            $request->getParsedBody(),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    public function delete(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $ok = $service->delete(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        if ($ok) {
            $response = $response->withStatus(204);
        }

        return $this->responseWithData($request, $response, []);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->findAll($request->getQueryParams());

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function createFolder(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->createFolder(
            $request->getParsedBody(),
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
    public function readFolder(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->findFolder(
            $request->getAttribute('id'),
            ArrayUtils::pick($request->getQueryParams(), ['fields', 'meta'])
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateFolder(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->updateFolder(
            $request->getAttribute('id'),
            $request->getParsedBody(),
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
    public function allFolder(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $responseData = $service->findAllFolders(
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
    public function deleteFolder(Request $request, Response $response)
    {
        $service = new FilesServices($this->container);
        $ok = $service->deleteFolder(
            $request->getAttribute('id')
        );

        if ($ok) {
            $response = $response->withStatus(204);
        }

        return $this->responseWithData($request, $response, []);
    }
}
