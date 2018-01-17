<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;
use Directus\Database\SchemaManager;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Exception\Http\BadRequestException;
use Directus\Services\EntriesService;
use Directus\Services\GroupsService;
use Directus\Util\ArrayUtils;

class Items extends Route
{
    use ActivityMode;

    public function __invoke(Application $app)
    {
        $app->map(['GET', 'POST'], '/{table}', [$this, 'all']);
        $app->map(['PUT', 'PATCH', 'GET'], '/{table}/{id}', [$this, 'one']);
        $app->delete('/{table}/{id}', [$this, 'delete']);
        $app->get('/{table}/{id}/meta', [$this, 'meta']);

        $app->map(['POST', 'PATCH', 'PUT', 'DELETE'], '/{table}/batch', [$this, 'batch']);
        // NOTE: /tables/:table/typeahead Removed. Make it work with filters. Otherwise will be added
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');
        $params = $request->getQueryParams();

        $this->throwErrorIfSystemTable($tableName);

        // TODO: Use repository instead of tablegateway
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        $primaryKey = $tableGateway->primaryKeyFieldName;

        if ($request->isPost()) {
            $this->validateRequestWithTable($request, $tableName);

            $entriesService = new EntriesService($this->container);
            $newRecord = $entriesService->createEntry($tableName, $request->getParams(), $params);
            $params['id'] = ArrayUtils::get($newRecord->toArray(), $primaryKey);
            $params['status'] = null;
        }

        $responseData = $this->getEntriesAndSetResponseCacheTags($tableGateway, $params);

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
    public function batch(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');

        $this->throwErrorIfSystemTable($tableName);

        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $rows = array_key_exists('rows', $payload) ? $payload['rows'] : false;
        $isDelete = $request->isDelete();
        $deleted = false;

        if (!is_array($rows) || count($rows) <= 0) {
            throw new \Exception(__t('rows_no_specified'));
        }

        $rowIds = [];
        $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        $primaryKeyFieldName = $tableGateway->primaryKeyFieldName;

        // hotfix add entries by bulk
        if ($request->isPost()) {
            $entriesService = new EntriesService($this->container);
            foreach($rows as $row) {
                $newRecord = $entriesService->createEntry($tableName, $row, $params);

                if (ArrayUtils::has($newRecord->toArray(), $primaryKeyFieldName)) {
                    $rowIds[] = $newRecord[$primaryKeyFieldName];
                }
            }
        } else {
            foreach ($rows as $row) {
                if (!array_key_exists($primaryKeyFieldName, $row)) {
                    throw new \Exception(__t('row_without_primary_key_field'));
                }

                array_push($rowIds, $row[$primaryKeyFieldName]);
            }

            $where = new \Zend\Db\Sql\Where;

            if ($isDelete) {
                // TODO: Implement this into a hook
                if ($tableName === 'directus_groups') {
                    $groupService = new GroupsService($this->container);
                    foreach ($rowIds as $id) {
                        $group = $groupService->find($id);

                        if ($group && !$groupService->canDelete($id)) {
                            $response = $response->withStatus(403);

                            return $this->responseWithData($request, $response, [
                                'error' => [
                                    'message' => sprintf('You are not allowed to delete group [%s]', $group->name)
                                ]
                            ]);
                        }
                    }
                }
                $deleted = $tableGateway->delete($where->in($primaryKeyFieldName, $rowIds));
            } else {
                foreach ($rows as $row) {
                    $tableGateway->updateCollection($row);
                }
            }
        }

        if (!empty($rowIds)) {
            $params['filters'] = [
                $primaryKeyFieldName => ['in' => $rowIds]
            ];
        }

        $entries = $this->getEntriesAndSetResponseCacheTags($tableGateway, $params);

        if ($isDelete) {
            $responseData = [];
            // TODO: Parse params into the expected value data type
            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $responseData['meta'] = ['table' => $tableGateway->getTable(), 'ids' => $rowIds];
            }

            // TODO: Add proper translated error
            if (!$deleted) {
                $responseData['error'] = ['message' => 'failed batch delete'];
            }
        } else {
            $responseData = ['data' => $entries];
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
    public function one(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');
        $id = $request->getAttribute('id');

        $this->throwErrorIfSystemTable($tableName);

        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $params['table_name'] = $tableName;
        $params['id'] = $id;

        $TableGateway = RelationalTableGateway::makeTableGatewayFromTableName($tableName, $dbConnection, $acl);
        switch ($request->getMethod()) {
            // PUT an updated table entry
            case 'PATCH':
            case 'PUT':
                $this->validateRequestWithTable($request, $tableName);
                $payload[$TableGateway->primaryKeyFieldName] = $id;
                $TableGateway->updateRecord($payload, $this->getActivityMode());
                break;
            // DELETE a given table entry
            case 'DELETE':
                if ($tableName === 'directus_groups') {
                    $groupService = new GroupsService($this->container);
                    $group = $groupService->find($id);
                    if ($group && !$groupService->canDelete($id)) {
                        $response = $response->withStatus(403);

                        return $this->responseWithData($request, $response, [
                            'error' => [
                                'message' => sprintf('You are not allowed to delete group [%s]', $group->name)
                            ]
                        ]);
                    }
                }
                $condition = [
                    $TableGateway->primaryKeyFieldName => $id
                ];

                if (ArrayUtils::get($params, 'soft')) {
                    if (!$TableGateway->getTableSchema()->hasStatusColumn()) {
                        throw new BadRequestException(__t('cannot_soft_delete_missing_status_column'));
                    }

                    $success = $TableGateway->update([
                        $TableGateway->getStatusColumnName() => $TableGateway->getDeletedValue()
                    ], $condition);
                } else {
                    $success =  $TableGateway->delete($condition);
                }

                $responseData = [];
                if (!$success) {
                    $responseData['error'] = [
                        'message' => __t('internal_server_error')
                    ];
                }

                return $this->responseWithData($request, $response, $responseData);
        }

        // TODO: Do not fetch if this is after insert
        // and the user doesn't have permission to read
        $responseData = $this->getEntriesAndSetResponseCacheTags($TableGateway, $params);

        if (!$responseData) {
            $responseData = [
                'error' => [
                    'message' => __t('unable_to_find_record_in_x_with_id_x', ['table' => $tableName, 'id' => $id])
                ],
            ];
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
        return $this->one($request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     */
    public function meta(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('table');

        $this->throwErrorIfSystemTable($tableName);

        $id = $request->getAttribute('id');
        $params = $request->getQueryParams();
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        $tableGateway = new DirectusActivityTableGateway($dbConnection, $acl);

        $metaData = $this->getDataAndSetResponseCacheTags([$tableGateway, 'getMetadata'], [$tableName, $id]);

        $responseData = [];
        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            $responseData['meta'] = ['table' => 'directus_activity', 'type' => 'item'];
        }

        $responseData['data'] = $metaData;

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
