<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Object\Column;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableSchema;
use Directus\Permissions\Exception\UnauthorizedTableAlterException;
use Directus\Services\ColumnsService;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use Zend\Db\Sql\Predicate\In;

class Columns extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/{table}', [$this, 'create']);
        $app->get('/{table}', [$this, 'all']);
        $app->get('/{table}/{column}', [$this, 'one']);
        $app->map(['PUT', 'PATCH'], '/{table}/{column}', [$this, 'update']);
        $app->post('/{table}/{column}', [$this, 'post']);
        $app->delete('/{table}/{column}', [$this, 'delete']);
        $app->map(['PATCH', 'PUT'], '/{table}/bulk', [$this, 'batch']);

        // Interfaces
        $app->get('/{table}/{column}/{ui}', [$this, 'ui']);
        $app->map(['POST', 'PUT', 'PATCH'], '/{table}/{column}/{ui}', [$this, 'updateUi']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws UnauthorizedTableAlterException
     */
    public function create(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $tableName = $request->getAttribute('table');

        $params['table_name'] = $tableName;

        $payload['table_name'] = $tableName;
        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $this->validateDataWithTable($request, $payload, 'directus_columns');

        /**
         * TODO: check if a column by this name already exists
         * TODO:  build this into the method when we shift its location to the new layer
         */
        if (!$acl->hasTablePrivilege($tableName, 'alter')) {
            throw new UnauthorizedTableAlterException(__t('permission_table_alter_access_forbidden_on_table', [
                'table_name' => $tableName
            ]));
        }

        $columnService = new ColumnsService($this->container);
        $params['column_name'] = $columnService->addColumn($tableName, $payload);

        $data = [];
        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $data['meta'] = ['type' => 'item', 'table' => 'directus_columns'];
        }

        $data['data'] = TableSchema::getColumnSchema($tableName, $params['column_name'], true)->toArray();

        return $this->withData($response, $data);
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
     */
    public function all(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $tableName = $request->getAttribute('table');
        $fields = $request->getQueryParam('fields');
        if (!is_array($fields)) {
            $fields = StringUtils::csv($fields);
        }

        $this->tagResponseCache('tableColumnsSchema_'.$tableName);
        $data = [];

        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $data['meta'] = ['type' => 'collection', 'table' => 'directus_columns'];
        }

        $data['data'] = array_map(function(Column $column) use ($fields) {
            $info = $column->toArray();

            if (!empty($fields)) {
                $info = ArrayUtils::pick($info, $fields);
            }

            return $info;
        }, TableSchema::getTableColumnsSchema($tableName));

        return $this->withData($response, $data);
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
        $TableGateway = new RelationalTableGateway('directus_columns', $dbConnection, $acl);
        $data = $payload;

        // TODO: check whether this condition is still needed
        if (isset($data['type'])) {
            $data['data_type'] = $data['type'];
            $data['relationship_type'] = $data['type'];
            unset($data['type']);
        }

        $data['column_name'] = $columnName;
        $data['table_name'] = $tableName;
        $row = $TableGateway->findOneByArray(['table_name' => $tableName, 'column_name' => $columnName]);

        if ($row) {
            $data['id'] = $row['id'];
        }

        $this->invalidateCacheTags(['tableColumnsSchema_'.$tableName, 'columnSchema_'.$tableName.'_'.$columnName]);
        $newRecord = $TableGateway->updateRecord($data, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);

        return $this->withData($response, $newRecord);
    }

    /**
     * Get information of a single table
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function one(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');
        $params = $request->getQueryParams();

        return $this->getColumnInfo($tableName, $columnName, $params);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');

        $params['column_name'] = $columnName;
        $params['table_name'] = $tableName;

        // This `type` variable is used on the client-side
        // Not need on server side.
        // TODO: We should probably stop using it on the client-side
        unset($payload['type']);
        // Add table name to dataset. TODO more clarification would be useful
        // Also This would return an Error because of $row not always would be an array.
        if ($payload) {
            foreach ($payload as &$row) {
                if (is_array($row)) {
                    $row['table_name'] = $tableName;
                }
            }
        }

        $columnsService = new ColumnsService($this->container);
        $columnsService->update($tableName, $columnName, $payload, $request->isPatch());

        return $this->getColumnInfo($response, $tableName, $columnName, $params);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function batch(Request $request, Response $response)
    {
        $container = $this->container;
        $dbConnection = $container->get('database');
        $acl = $container->get('acl');
        $TableGateway = new RelationalTableGateway('directus_columns', $dbConnection, $acl);
        $payload = $request->getParsedBody();
        $tableName = $request->getAttribute('table');
        $params = $request->getQueryParams();

        $rows = array_key_exists('rows', $payload) ? $payload['rows'] : false;
        if (!is_array($rows) || count($rows) <= 0) {
            throw new \Exception(__t('rows_no_specified'));
        }

        $columnNames = [];
        foreach ($rows as $row) {
            $column = $row['id'];
            $columnNames[] = $column;
            unset($row['id']);

            $condition = [
                'table_name' => $tableName,
                'column_name' => $column
            ];

            if ($row) {
                // check if the column data exists in `directus_columns`
                $columnInfo = $TableGateway->select($condition);
                if ($columnInfo->count() === 0) {
                    // add the column information into `directus_columns`
                    $columnInfo = TableSchema::getColumnSchema($tableName, $column);
                    $columnInfo = $columnInfo->toArray();
                    $columnsName = TableSchema::getAllTableColumnsName('directus_columns');
                    // NOTE: the column name name is data_type in the database
                    // but the attribute in the object is type
                    // change the name to insert the data type value into the columns table
                    ArrayUtils::rename($columnInfo, 'type', 'data_type');
                    $columnInfo = ArrayUtils::pick($columnInfo, $columnsName);
                    // NOTE: remove the column id info
                    // this information is the column name
                    // not the record id in the database
                    ArrayUtils::remove($columnInfo, ['id', 'options']);
                    $TableGateway->insert($columnInfo);
                }

                $TableGateway->update($row, $condition);
            }
        }

        $rows = $TableGateway->select([
            'table_name' => $tableName,
            new In('column_name', $columnNames)
        ]);

        $responseBody = [];
        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $responseBody['meta'] = [
                'table' => 'directus_columns',
                'type' => 'collection'
            ];
        }

        $responseBody['data'] = $rows->toArray();

        return $this->withData($response, $responseBody);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        // TODO: make sure to check aliases column (Ex: M2M Columns)
        $tableObject = TableSchema::getTableSchema($tableName, [], false, true);
        $hasColumn = $tableObject->hasColumn($columnName);
        $hasMoreThanOneColumn = count($tableObject->getColumns()) > 1;
        $success = false;

        if ($hasColumn && $hasMoreThanOneColumn) {
            $success = $tableGateway->dropColumn($columnName);
        }

        // NOTE: Let's call it response body, it will make more sense, right?
        $responseBody = [];
        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $responseBody['meta'] = [
                'table' => $tableGateway->getTable(),
                'column' => $columnName
            ];
        }

        if (!$success) {
            if (!$hasMoreThanOneColumn) {
                $errorMessage = __t('cannot_remove_last_column');
            } else if (!$hasColumn) {
                $errorMessage = __t('unable_to_find_column_X_in_table_y', [
                    'table' => $tableName,
                    'column' => $columnName
                ]);
            } else {
                $errorMessage = __t('unable_to_remove_column_x', [
                    'column_name' => $columnName
                ]);
            }

            $responseBody['error'] = [
                'message' => $errorMessage
            ];
        }

        return $this->withData($response, $responseBody);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateUi(Request $request, Response $response)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableGateway = new RelationalTableGateway('directus_columns', $dbConnection, $acl);

        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');
        $ui = $request->getAttribute('ui');

        switch ($request->getMethod()) {
            case 'PATH': // New Not used before
            case 'PUT':
            case 'POST':
                $payload = $request->getParsedBody();

                $columnData = $tableGateway->select([
                    'table_name' => $tableName,
                    'column_name' => $columnName
                ])->current();

                if ($columnData) {
                    $columnData = $columnData->toArray();

                    $data = [
                        'id' => $columnData['id'],
                        'options' => json_encode($payload)
                    ];

                    $tableGateway->updateCollection($data);
                }
        }

        return $this->getUiInfo($response, $tableName, $columnName, $ui, $request->getQueryParams());
    }

    /**
     * Get given table.column.ui information
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function ui(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');
        $columnName = $request->getAttribute('column');
        $ui = $request->getAttribute('ui');

        return $this->getUiInfo($response, $tableName, $columnName, $ui, $request->getQueryParams());
    }

    /**
     * @param Response $response
     * @param string $tableName
     * @param string $columnName
     * @param array $params
     *
     * @return Response
     */
    public function getColumnInfo(Response $response, $tableName, $columnName, array $params = [])
    {
        $result = $this->fetchColumnInfo($tableName, $columnName);

        if (!$result) {
             $responseBody = [
                'error' => [
                    'message' => __t('unable_to_find_column_x', ['column' => $columnName])
                ]
            ];
        } else {
            $responseBody = [];
            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $responseBody['meta'] = [
                    'type' => 'collection',
                    'table' => 'directus_columns'
                ];
            }

            $responseBody['data'] = $result;
        }

        return $this->withData($response, $responseBody);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return Column
     */
    public function fetchColumnInfo($tableName, $columnName)
    {
        $this->tagResponseCache(['tableColumnsSchema_'.$tableName, 'columnSchema_'.$tableName.'_'.$columnName]);

        return TableSchema::getColumnSchema($tableName, $columnName, true);
    }

    /**
     * @param $response
     * @param $tableName
     * @param $columnName
     * @param $ui
     * @param array $params
     *
     * @return Response
     */
    public function getUiInfo($response, $tableName, $columnName, $ui, $params = [])
    {
        $result = $this->fetchUiInfo($tableName, $columnName);

        if (!$result) {
            $response = $response->withStatus(404);
            $data = [
                'error' => [
                    'message' => __t('unable_to_find_column_x_options_for_x', ['column' => $columnName, 'ui' => $ui])
                ]
            ];
        } else {
            $result = $response->toArray();

            $data = [];
            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $data['meta'] = ['table' => 'directus_columns', 'type' => 'item'];
            }

            $data = [
                'data' => json_decode($result['options'], true)
            ];
        }

        return $this->withData($response, $data);
    }

    /**
     * Gets the Interface options
     *
     * @param $tableName
     * @param $columnName
     *
     * @return null|array
     */
    public function fetchUiInfo($tableName, $columnName)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableGateway = new RelationalTableGateway('directus_columns', $dbConnection, $acl);

        $select = $tableGateway->getSql()->select();
        $select->columns(['id', 'options']);
        $select->where([
            'table_name' => $tableName,
            'column_name' => $columnName
        ]);

        $result = $this->getDataAndSetResponseCacheTags(
            [$tableGateway, 'selectWith'],
            [$select]
        )->current();

        return $result ? $result->toArray() : null;
    }
}
