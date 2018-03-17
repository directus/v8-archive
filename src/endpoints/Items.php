<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;
use Directus\Database\Schema\SchemaManager;
use Directus\Exception\Http\BadRequestException;
use Directus\Services\ItemsService;

class Items extends Route
{
    public function __invoke(Application $app)
    {
        $app->get('/{collection}', [$this, 'all']);
        $app->post('/{collection}', [$this, 'create']);
        $app->get('/{collection}/{id}', [$this, 'read']);
        $app->patch('/{collection}/{id}', [$this, 'update']);
        $app->delete('/{collection}/{id}', [$this, 'delete']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $collection = $request->getAttribute('collection');
        $params = $request->getQueryParams();

        $this->throwErrorIfSystemTable($collection);

        $itemsService = new ItemsService($this->container);
        $responseData = $itemsService->findAll($collection, $params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        if (isset($payload[0]) && is_array($payload[0])) {
            return $this->batch($request, $response);
        }

        $collection = $request->getAttribute('collection');
        $this->throwErrorIfSystemTable($collection);

        $itemsService = new ItemsService($this->container);
        $responseData = $itemsService->createItem(
            $collection,
            $payload,
            $request->getQueryParams()
        );

        if (is_null($responseData)) {
            $response = $response->withStatus(204);
            $responseData = [];
        }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function read(Request $request, Response $response)
    {
        $collection = $request->getAttribute('collection');
        $this->throwErrorIfSystemTable($collection);

        $itemsService = new ItemsService($this->container);
        $responseData = $itemsService->find(
            $collection,
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
     *
     * @throws BadRequestException
     */
    public function update(Request $request, Response $response)
    {
        $collection = $request->getAttribute('collection');

        $this->throwErrorIfSystemTable($collection);

        $id = $request->getAttribute('id');

        if (strpos($id, ',') !== false) {
            return $this->batch($request, $response);
        }

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        $itemsService = new ItemsService($this->container);
        $responseData = $itemsService->update($collection, $id, $payload, $params);

        if (is_null($responseData)) {
            $response = $response->withStatus(204);
            $responseData = [];
        }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function delete(Request $request, Response $response)
    {
        $collection = $request->getAttribute('collection');
        $this->throwErrorIfSystemTable($collection);

        $id = $request->getAttribute('id');
        if (strpos($id, ',') !== false) {
            return $this->batch($request, $response);
        }

        $itemsService = new ItemsService($this->container);
        $itemsService->delete($collection, $request->getAttribute('id'), $request->getQueryParams());

        $responseData = [];
        $response = $response->withStatus(204);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    protected function batch(Request $request, Response $response)
    {
        $collection = $request->getAttribute('collection');

        $this->throwErrorIfSystemTable($collection);

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        $responseData = null;
        $itemsService = new ItemsService($this->container);
        if ($request->isPost()) {
            $responseData = $itemsService->batchCreate($collection, $payload, $params);
        } else if ($request->isPatch()) {
            $ids = explode(',', $request->getAttribute('id'));
            $responseData = $itemsService->batchUpdateWithIds($collection, $ids, $payload, $params);
        } else if ($request->isDelete()) {
            $ids = explode(',', $request->getAttribute('id'));
            $itemsService->batchDeleteWithIds($collection, $ids, $params);
        }

        if (empty($responseData)) {
            $response = $response->withStatus(204);
            $responseData = [];
        }

        return $this->responseWithData($request, $response, $responseData);
    }

    protected function throwErrorIfSystemTable($name)
    {
        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        if (in_array($name, $schemaManager->getSystemTables())) {
            throw new ForbiddenSystemTableDirectAccessException($name);
        }
    }
}
