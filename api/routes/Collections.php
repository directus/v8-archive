<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Exception\TableAlreadyExistsException;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Database\TableGateway\DirectusTablesTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableSchema;
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
        $app->delete('/{table}', [$this, 'delete']);
        $app->get('/{table}', [$this, 'one']);
        $app->map(['PATCH', 'PUT'], '/{table}', 'update');
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
        $tables = TableSchema::getTablenames($params);

        $responseData = [];

        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $responseData['meta'] = [
                'type' => 'collection',
                'table' => 'directus_tables'
            ];
        }

        $responseData['data'] = $tables;

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
        $responseData = $this->getInfo($request->getAttribute('table'), $request->getQueryParams());

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
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $payload = $request->getParsedBody();
        $tableName = $request->getAttribute('table');
        $tableGateway = new DirectusTablesTableGateway($dbConnection, $acl);

        $payload['table_name'] = $tableName;

        // NOTE: Do not recall the reason of this. refresh mind next round
        if ($request->isPut()) {
            $table_settings = [
                'table_name' => $payload['table_name'],
                'hidden' => (int)$payload['hidden'],
                'single' => (int)$payload['single'],
                'footer' => (int)$payload['footer'],
                'primary_column' => array_key_exists('primary_column', $payload) ? $payload['primary_column'] : ''
            ];
        } else {
            $table_settings = ArrayUtils::omit($payload, 'columns');
        }

        // TODO: Possibly pretty this up so not doing direct inserts/updates
        $set = $tableGateway->select(['table_name' => $tableName])->toArray();

        // If item exists, update, else insert
        if (count($set) > 0) {
            $tableGateway->update($table_settings, ['table_name' => $tableName]);
        } else {
            $tableGateway->insert($table_settings);
        }

        $responseData = $this->getInfo($tableName);

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
    public function create(Request $request, Response $response)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableName = $request->getParsedBodyParam('table_name');
        $systemColumns = $request->getParsedBodyParam('columns', []);

        $this->validateRequestWithTable($request, 'directus_tables');

        if ($acl->getGroupId() != 1) {
            throw new \Exception(__t('permission_denied'));
        }

        $tableService = new TablesService($this->container);

        try {
            $tableService->createTable($tableName, $systemColumns);
        } catch (TableAlreadyExistsException $e) {
            return $this->responseWithData($request, $response, [
                'error' => [
                    'message' => __t('table_x_already_exists', [
                        'table_name' => $tableName
                    ])
                ]
            ]);
        }

        $privilegesTableGateway = new DirectusPrivilegesTableGateway($dbConnection, $acl);
        $privileges = array_merge(Acl::PERMISSION_FULL, [
            'table_name' => $tableName,
            'nav_listed' => 1,
            'status_id' => null,
            'group_id' => 1
        ]);

        $privilegesTableGateway->insertPrivilege($privileges);
        $acl->setTablePrivileges($tableName, $privileges);

        $responseData = $this->getInfo($tableName, $request->getQueryParams());

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
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableName = $request->getAttribute('table');
        // TODO: How are we going to handle unmanage
        $unmanaged = $request->getQueryParam('unmanage', 0);

        if ($unmanaged == 1) {
            $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
            $success = $tableGateway->stopManaging();
        } else {
            $tableService = new TablesService($this->container);
            $success = $tableService->dropTable($tableName);
        }

        $responseData = [];
        if (!$success) {
            $responseData = [
                'error' => [
                    'message' => __t('unable_to_remove_table_x', ['table_name' => $tableName])
                ]
            ];
        }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param string $tableName
     * @param array $params
     *
     * @return array
     */
    public function getInfo($tableName, $params = [])
    {
        $this->tagResponseCache(['tableSchema_'.$tableName, 'table_directus_columns']);
        $result = TableSchema::getTable($tableName, $params);

        if (!$result) {
            $data = [
                'error' => [
                    'message' => __t('unable_to_find_table_x', ['table_name' => $tableName])
                ]
            ];
        } else {
            $data = [];

            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $data['meta'] = [
                    'type' => 'entry',
                    'table' => 'directus_tables'
                ];
            }

            $data['data'] = $result;
        }

        return $data;
    }
}
