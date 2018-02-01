<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\DirectusCollectionsTableGateway;
use Directus\Exception\UnauthorizedException;
use Directus\Permissions\Acl;
use Directus\Services\TablesService;
use Directus\Util\ArrayUtils;

class Collections extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{name}', [$this, 'one']);
        $app->patch('/{name}', [$this, 'update']);
        $app->delete('/{name}', [$this, 'delete']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function create(Request $request, Response $response)
    {
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $tableName = 'directus_collections';
        $tableObject = $schemaManager->getTableSchema($tableName);
        $constraints = $this->createConstraintFor($tableName, $tableObject->getColumnsName());
        $this->validate($payload, array_merge(['fields' => 'array'], $constraints));

        $tableService = new TablesService($this->container);
        $name = ArrayUtils::get($payload, 'collection');
        $data = ArrayUtils::omit($payload, 'collection');
        $table = $tableService->createTable($name, $data);

        $collectionTableGateway = $tableService->createTableGateway($tableName);
        $tableData = $collectionTableGateway->parseRecord($table->toArray());
        $responseData = $collectionTableGateway->wrapData($tableData, true, ArrayUtils::get($params, 'meta'));

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        // $tables = TableSchema::getTablenames($params);

        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableGateway = new DirectusCollectionsTableGateway($dbConnection, $acl);
        $responseData = $tableGateway->getItems($params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function one(Request $request, Response $response)
    {
        $name = $request->getAttribute('name');
        $this->validate(['collection' => $name], ['collection' => 'required|string']);
        $responseData = $this->getInfo($name, $request->getQueryParams());

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
        // TODO: Add only for admin middleware
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $params = $request->getQueryParams();
        $payload = $request->getParsedBody();

        // Validates the table name
        $collectionName = $request->getAttribute('name');
        $this->validate(['collection' => $collectionName], ['collection' => 'required|string']);

        // Validates payload data
        $tableName = 'directus_collections';
        $tableObject = $schemaManager->getTableSchema($tableName);
        $constraints = $this->createConstraintFor($tableName, $tableObject->getColumnsName());
        $payload['collection'] = $collectionName;
        $this->validate($payload, array_merge(['fields' => 'array'], $constraints));

        $dbConnection = $this->container->get('database');
        $tableGateway = new DirectusCollectionsTableGateway($dbConnection, $acl);

        // TODO: Create a check if exists method (quicker) + not found exception
        $tableGateway->loadItems(['id' => $collectionName]);

        // Update Schema
        $tableService = new TablesService($this->container);
        $name = ArrayUtils::get($payload, 'collection');
        $data = ArrayUtils::omit($payload, 'collection');
        $collection = $tableService->updateTable($name, $data);
        $responseData = $tableGateway->wrapData(
            $collection->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
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
        $collectionName = $request->getAttribute('name');
        $this->validate(['name' => $collectionName], ['name' => 'required|string']);
        // TODO: How are we going to handle unmanage
        // $unmanaged = $request->getQueryParam('unmanage', 0);
        // if ($unmanaged == 1) {
        //     $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        //     $success = $tableGateway->stopManaging();
        // }

        $tableService = new TablesService($this->container);
        $tableService->dropTable($collectionName);

        return $this->responseWithData($request, $response, []);
    }

    /**
     * @param string $tableName
     * @param array $params
     *
     * @return array
     */
    public function getInfo($tableName, $params = [])
    {
        // $this->tagResponseCache(['tableSchema_'.$tableName, 'table_directus_columns']);
        // $result = TableSchema::getTable($tableName, $params);
        //
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $tableGateway = new DirectusCollectionsTableGateway($dbConnection, $acl);
        //
        // return $tableGateway->wrapData($result, true, ArrayUtils::get($params, 'meta', 0));

        return $tableGateway->getItems([
            'id' => $tableName
        ]);
    }
}
