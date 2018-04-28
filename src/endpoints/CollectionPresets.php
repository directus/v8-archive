<?php

namespace Directus\Api\Routes;

use Directus\Api\Services\PreferencesService;
use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\TableGateway\DirectusCollectionPresetsTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Services\CollectionPresetsService;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use Zend\Db\RowGateway\RowGateway;

class CollectionPresets extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'read']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $service = new CollectionPresetsService($this->container);

        $responseData = $service->findAll($request->getQueryParams());

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
        $service = new CollectionPresetsService($this->container);
        $responseData = $service->find(
            $request->getAttribute('id'),
            ArrayUtils::pick($request->getQueryParams(), ['meta', 'fields'])
        );

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
        $service = new CollectionPresetsService($this->container);
        $responseData = $service->createItem(
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
    public function update(Request $request, Response $response)
    {
        $service = new CollectionPresetsService($this->container);
        $responseData = $service->update(
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
    public function delete(Request $request, Response $response)
    {
        $service = new CollectionPresetsService($this->container);
        $service->delete(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        $response = $response->withStatus(204);

        return $this->responseWithData($request, $response, []);
    }
}
