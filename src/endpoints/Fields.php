<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Exception\ColumnNotFoundException;
use Directus\Database\Exception\TableNotFoundException;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableSchema;
use Directus\Exception\ErrorException;
use Directus\Exception\UnauthorizedException;
use Directus\Permissions\Acl;
use Directus\Services\ColumnsService;
use Directus\Services\TablesService;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;

class Fields extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/{collection}', [$this, 'create']);
        $app->get('/{collection}/{field}', [$this, 'read']);
        $app->patch('/{collection}/{field}', [$this, 'update']);
        $app->delete('/{collection}/{field}', [$this, 'delete']);
        $app->get('/{collection}', [$this, 'all']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws UnauthorizedException
     */
    public function create(Request $request, Response $response)
    {
        $service = new TablesService($this->container);
        $payload = $request->getParsedBody();
        $field = ArrayUtils::pull($payload, 'field');

        $responseData = $service->addColumn(
            $request->getAttribute('collection'),
            $field,
            $payload,
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
     * @throws ColumnNotFoundException
     * @throws TableNotFoundException
     * @throws UnauthorizedException
     */
    public function read(Request $request, Response $response)
    {
        $collectionName = $request->getAttribute('collection');
        $fieldName = $request->getAttribute('field');

        $service = new TablesService($this->container);
        $responseData = $service->findField($collectionName, $fieldName, $request->getQueryParams());

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws UnauthorizedException
     */
    public function update(Request $request, Response $response)
    {
        $service = new TablesService($this->container);

        $responseData = $service->changeColumn(
            $request->getAttribute('collection'),
            $request->getAttribute('field'),
            $request->getParsedBody(),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * Get all columns that belong to a given table
     *
     * NOTE: Maybe this should be on /tables instead
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws TableNotFoundException
     * @throws UnauthorizedException
     */
    public function all(Request $request, Response $response)
    {
        $service = new TablesService($this->container);
        $responseData = $service->findAllFields(
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
     *
     * @throws ErrorException
     * @throws UnauthorizedException
     */
    public function delete(Request $request, Response $response)
    {
        $service = new TablesService($this->container);

        $ok = $service->deleteField(
            $request->getAttribute('collection'),
            $request->getAttribute('field'),
            $request->getQueryParams()
        );

        if ($ok) {
            $response = $response->withStatus(204);
        }

        return $this->responseWithData($request, $response, []);
    }
}
