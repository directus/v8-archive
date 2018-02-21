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
        $app->get('/{collection}/{field}', [$this, 'one']);
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
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $collectionName = $request->getAttribute('collection');
        $payload['collection'] = $collectionName;

        // if (!$acl->hasTablePrivilege($collectionName, 'alter')) {
        //     throw new UnauthorizedTableAlterException(__t('permission_table_alter_access_forbidden_on_table', [
        //         'collection' => $tableName
        //     ]));
        // }

        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $this->validate(['collection' => $collectionName], ['collection' => 'required|string']);
        $this->validateDataWithTable($request, $payload, 'directus_fields');

        $tableService = new TablesService($this->container);
        $fieldName = ArrayUtils::pull($payload, 'field');
        $field = $tableService->addColumn($collectionName, $fieldName, $payload);

        $dbConnection = $this->container->get('database');
        $responseData = (new RelationalTableGateway('directus_fields', $dbConnection, $acl))->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
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
    public function one(Request $request, Response $response)
    {
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $collectionName = $request->getAttribute('collection');
        $fieldName = $request->getAttribute('field');
        $this->validate([
            'collection' => $collectionName,
            'field' => $fieldName
        ], [
            'collection' => 'required|string',
            'field' => 'required|string'
        ]);

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $tableObject = $schemaManager->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getField($fieldName);
        if (!$columnObject) {
            throw new ColumnNotFoundException($fieldName);
        }

        $dbConnection = $this->container->get('database');
        $tableGateway = new RelationalTableGateway('directus_fields', $dbConnection, $acl);

        $params = ArrayUtils::pick($request->getQueryParams(), ['meta', 'fields']);
        $params['single'] = true;
        $params['filter'] = [
            'collection' => $collectionName,
            'field' => $fieldName
        ];

        $responseData = $tableGateway->getItems($params);

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
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $payload['collection'] = $collectionName = $request->getAttribute('collection');
        $payload['field'] = $fieldName = $request->getAttribute('field');

        // if (!$acl->hasTablePrivilege($collectionName, 'alter')) {
        //     throw new UnauthorizedTableAlterException(__t('permission_table_alter_access_forbidden_on_table', [
        //         'collection' => $tableName
        //     ]));
        // }

        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $this->validate([
            'collection' => $collectionName,
            'field' => $fieldName,
            'payload' => $payload
        ], [
            'collection' => 'required|string',
            'field' => 'required|string',
            'payload' => 'required|array'
        ]);

        $tableService = new TablesService($this->container);
        $fieldName = ArrayUtils::pull($payload, 'field');
        $field = $tableService->changeColumn($collectionName, $fieldName, $payload);

        $dbConnection = $this->container->get('database');
        $responseData = (new RelationalTableGateway('directus_fields', $dbConnection, $acl))->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
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
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $params = $request->getQueryParams();
        $fields = $request->getQueryParam('fields', '*');
        if (!is_array($fields)) {
            $fields = StringUtils::csv($fields);
        }

        $collectionName = $request->getAttribute('collection');

        // $this->tagResponseCache('tableColumnsSchema_'.$tableName);
        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $tableObject = $schemaManager->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $collectionTableObject = $schemaManager->getTableSchema('directus_fields');
        if (in_array('*', $fields)) {
            $fields = $collectionTableObject->getFieldsName();
        }

        $fields = array_map(function(Field $column) use ($fields) {
            $data = $column->toArray();

            if (!empty($fields)) {
                $data = ArrayUtils::pick($data, $fields);
            }

            return $data;
        }, TableSchema::getTableColumnsSchema($collectionName));

        $dbConnection = $this->container->get('database');
        $tableGateway = new RelationalTableGateway('directus_fields', $dbConnection, $acl);
        $responseData = $tableGateway->wrapData($fields, false, ArrayUtils::get($params, 'meta'));

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * NOTE: This is a endpoint that needs to be merge with the update endpoint
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function post(Request $request, Response $response)
    {
        $container = $this->container;
        $dbConnection = $container->get('database');
        $acl = $container->get('acl');
        $payload = $request->getParsedBody();
        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');
        $TableGateway = new RelationalTableGateway('directus_fields', $dbConnection, $acl);

        // TODO: check whether this condition is still needed
        if (isset($payload['type'])) {
            $payload['data_type'] = $payload['type'];
            $payload['relationship_type'] = $payload['type'];
            unset($payload['type']);
        }

        $payload['field'] = $columnName;
        $payload['collection'] = $tableName;
        $row = $TableGateway->findOneByArray(['collection' => $tableName, 'field' => $columnName]);

        if ($row) {
            $payload['id'] = $row['id'];
        }

        $this->invalidateCacheTags(['tableColumnsSchema_'.$tableName, 'columnSchema_'.$tableName.'_'.$columnName]);
        $newRecord = $TableGateway->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);

        return $this->responseWithData($request, $response, $newRecord->toArray());
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
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        if (!$acl->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $collectionName = $request->getAttribute('collection');
        $fieldName = $request->getAttribute('field');
        $this->validate([
            'collection' => $collectionName,
            'field' => $fieldName
        ], [
            'collection' => 'required|string',
            'field' => 'required|string'
        ]);

        $tableService = new TablesService($this->container);
        $tableService->dropColumn($collectionName, $fieldName);

        return $this->responseWithData($request, $response, []);
    }
}
