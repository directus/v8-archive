<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Services\GroupsService;
use Directus\Util\ArrayUtils;

class Groups extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->map(['GET', 'POST'], '', [$this, 'all']);
        $app->get('/{id}', [$this, 'one']);
        $app->patch('/{id}', [$this, 'patch']);
        $app->delete('/{id}', [$this, 'delete']);
        // TODO: Missing PUT
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $container = $this->container;
        $acl = $container->get('acl');
        $dbConnection = $container->get('database');
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        // TODO need PUT
        $tableName = 'directus_groups';
        $GroupsTableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);

        switch ($request->getMethod()) {
            case 'POST':
                $newRecord = $GroupsTableGateway->updateRecord($payload);
                $newGroupId = $newRecord['id'];
                $data = $GroupsTableGateway->getEntries(['id' => $newGroupId]);
                break;
            case 'GET':
            default:
                $data = $this->getEntriesAndSetResponseCacheTags($GroupsTableGateway, $params);
        }

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function one(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $params = $request->getQueryParams();
        $id = $request->getAttribute('id');
        $params['id'] = $id;

        $tableName = 'directus_groups';
        $Groups = new RelationalTableGateway($tableName, $dbConnection, $acl);
        $data = $this->getEntriesAndSetResponseCacheTags($Groups, $params);

        if (!$data) {
            $data = [
                'error' => [
                    'message' => __t('unable_to_find_group_with_id_x', ['id' => $id])
                ]
            ];
        }

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function patch(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('zenddb');
        $payload = $request->getParsedBody();
        $id = $request->getAttribute('id');

        $tableName = 'directus_groups';
        $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        $payload['id'] = $id;

        ArrayUtils::remove($payload, 'permissions');

        $newRecord = $tableGateway->updateRecord($payload);
        $newGroupId = $newRecord['id'];
        $data = $this->getEntriesAndSetResponseCacheTags($tableGateway, ['id' => $newGroupId]);

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function delete(Request $request, Response $response)
    {
        $groupService = new GroupsService($this->container);
        $id = $request->getAttribute('id');

        $group = $groupService->find($id);
        if (!$group) {
            $response = $response->withStatus(404);

            return $this->withData($response, [
                'error' => [
                    'message' => sprintf('Group [%d] not found', $id)
                ]
            ]);
        }

        if (!$groupService->canDelete($id)) {
            $response = $response->withStatus(403);

            return $this->withData($response, [
                'error' => [
                    'message' => sprintf('You are not allowed to delete group [%s]', $group->name)
                ]
            ]);
        }

        return $this->withData($response, []);
    }
}
