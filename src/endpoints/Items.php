<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Exception\Http\BadRequestException;
use Directus\Services\ItemsService;
use Directus\Services\GroupsService;
use Directus\Util\ArrayUtils;

class Items extends Route
{
    public function __invoke(Application $app)
    {
        $app->get('/{collection}', [$this, 'all']);
        $app->post('/{collection}', [$this, 'create']);
        $app->get('/{collection}/{id}', [$this, 'read']);
        $app->patch('/{collection}/{id}', [$this, 'update']);
        $app->delete('/{collection}/{id}', [$this, 'delete']);

        $app->map(['POST', 'PATCH', 'PUT', 'DELETE'], '/{collection}/batch', [$this, 'batch']);
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
        $tableName = $request->getAttribute('collection');

        $this->throwErrorIfSystemTable($tableName);

        $itemsService = new ItemsService($this->container);
        $responseData = $itemsService->createItem(
            $tableName,
            $request->getParsedBody(),
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
    public function batch(Request $request, Response $response)
    {
        $tableName = $request->getAttribute('collection');

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
        $itemsService = new ItemsService($this->container);

        // hotfix add entries by bulk
        if ($request->isPost()) {
            foreach($rows as $row) {
                $newRecord = $itemsService->createItem($tableName, $row, $params);

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
                // TODO: Create a batch delete method
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

        $entries = $itemsService->findAll($tableName, $params);

        if ($isDelete) {
            $responseData = [];
            // TODO: Parse params into the expected value data type
            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $responseData['meta'] = ['collection' => $tableGateway->getTable(), 'ids' => $rowIds];
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

    protected function throwErrorIfSystemTable($name)
    {
        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        if (in_array($name, $schemaManager->getSystemTables())) {
            throw new ForbiddenSystemTableDirectAccessException($name);
        }
    }
}
