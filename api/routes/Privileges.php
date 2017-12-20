<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Exception\Http\ForbiddenException;
use Directus\Util\StringUtils;

class Privileges extends Route
{
    public function __invoke(Application $app)
    {
        $app->get('/{group}[/{table}]', [$this, 'show']);
        $app->post('/{group}', [$this, 'create']);
        // $app->get('/privileges/:groupId(/:tableName)/?', '\Directus\API\Routes\A1\Privileges:showPrivileges');
        // $app->post('/privileges/:groupId/?', '\Directus\API\Routes\A1\Privileges:createPrivileges');
        // $app->put('/privileges/:groupId/:privilegeId/?', '\Directus\API\Routes\A1\Privileges:updatePrivileges');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws ForbiddenException
     */
    public function show(Request $request, Response $response)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        if ($acl->getGroupId() != 1) {
            throw new ForbiddenException(__t('permission_denied'));
        }

        $privileges = new DirectusPrivilegesTableGateway($dbConnection, $acl);
        $groupId = $request->getAttribute('group');
        $tableName = $request->getAttribute('table');
        $fields = $request->getQueryParam('fields', []);
        if (!is_array($fields)) {
            $fields = array_filter(StringUtils::csv($fields));
        }

        $data = $this->getDataAndSetResponseCacheTags(
            [$privileges, 'fetchPerTable'],
            [$groupId, $tableName, $fields]
        );

        $result = [];

        if ($request->getQueryParam('meta', 0) == 1) {
            $result['meta'] = [
                'type' => 'item',
                'table' => 'directus_privileges'
            ];
        }

        $result['data'] = $data;

        if (!$result['data']) {
            $response = $response->withStatus(404);

            $result = [
                'error' => [
                    'message' => __t('unable_to_find_privileges_for_x_in_group_y', ['table' => $tableName, 'group_id' => $groupId])
                ]
            ];
        }

        return $this->withData($response, $result);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws ForbiddenException
     */
    public function create(Request $request, Response $response)
    {
        $app = $this->app;
        $ZendDb = $app->container->get('zenddb');
        $acl = $app->container->get('acl');
        $requestPayload = $app->request()->post();

        if ($acl->getGroupId() != 1) {
            throw new ForbiddenException(__t('permission_denied'));
        }

        if (isset($requestPayload['addTable'])) {
            $tableName = ArrayUtils::get($requestPayload, 'table_name');

            ArrayUtils::remove($requestPayload, 'addTable');

            $tableService = new TablesService($app);

            try {
                $tableService->createTable($tableName, ArrayUtils::get($requestPayload, 'columnsName'));
            } catch (TableAlreadyExistsException $e) {
                // ----------------------------------------------------------------------------
                // Setting the primary key column interface
                // NOTE: if the table already exists but not managed by directus
                // the primary key interface is added to its primary column
                // ----------------------------------------------------------------------------
                $columnService = new ColumnsService($app);
                $tableObject = $tableService->getTableObject($tableName);
                $primaryColumn = $tableObject->getPrimaryColumn();
                if ($primaryColumn) {
                    $columnObject = $columnService->getColumnObject($tableName, $primaryColumn);
                    if (!TableSchema::isSystemColumn($columnObject->getUI())) {
                        $data = $columnObject->toArray();
                        $data['ui'] = SchemaManager::INTERFACE_PRIMARY_KEY;
                        $columnService->update($tableName, $primaryColumn, $data);
                    }
                }
            }

            ArrayUtils::remove($requestPayload, 'columnsName');
        }

        $privileges = new DirectusPrivilegesTableGateway($ZendDb, $acl);
        $response = $privileges->insertPrivilege($requestPayload);

        return $this->app->response([
            'meta' => [
                'type' => 'entry',
                'table' => 'directus_privileges'
            ],
            'data' => $response
        ]);
    }

    public function updatePrivileges($groupId, $privilegeId)
    {
        $app = $this->app;
        $ZendDb = $app->container->get('zenddb');
        $acl = $app->container->get('acl');
        $requestPayload = $app->request()->post();

        $requestPayload['id'] = $privilegeId;
        if ($acl->getGroupId() != 1) {
            throw new ForbiddenException(__t('permission_denied'));
        }

        $privileges = new DirectusPrivilegesTableGateway($ZendDb, $acl);

        if (isset($requestPayload['activeState'])) {
            if ($requestPayload['activeState'] !== 'all') {
                $priv = $privileges->findByStatus($requestPayload['table_name'], $requestPayload['group_id'], $requestPayload['activeState']);
                if ($priv) {
                    $requestPayload['id'] = $priv['id'];
                    $requestPayload['status_id'] = $priv['status_id'];
                } else {
                    unset($requestPayload['id']);
                    $requestPayload['status_id'] = $requestPayload['activeState'];
                    $response = $privileges->insertPrivilege($requestPayload);
                    return $this->app->response($response);
                }
            }
        }

        $response = $privileges->updatePrivilege($requestPayload);

        return $this->app->response([
            'data' => $response
        ]);
    }
}
