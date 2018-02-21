<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Services\GroupsService;
use Directus\Util\ArrayUtils;

class Groups extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'one']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
        $app->get('', [$this, 'all']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $groupsTableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);

        $newGroup = $groupsTableGateway->updateRecord($payload);
        $responseData = $groupsTableGateway->wrapData(
            $newGroup->toArray(),
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
    public function one(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $groupsTableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);

        $params = ArrayUtils::pick($request->getQueryParams(), ['fields', 'meta']);
        $params['id'] = $request->getAttribute('id');
        $responseData = $this->getEntriesAndSetResponseCacheTags($groupsTableGateway, $params);

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
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $groupsTableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);

        $payload['id'] = $request->getAttribute('id');
        $group = $groupsTableGateway->updateRecord($payload);

        $responseData = $groupsTableGateway->wrapData(
            $group->toArray(),
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
    public function all(Request $request, Response $response)
    {
        $container = $this->container;
        $acl = $container->get('acl');
        $dbConnection = $container->get('database');
        $params = $request->getQueryParams();

        $groupsTableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);
        $responseData = $this->getEntriesAndSetResponseCacheTags($groupsTableGateway, $params);

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
        $groupService = new GroupsService($this->container);
        $id = $request->getAttribute('id');

        $group = $groupService->find($id);
        if (!$group) {
            $response = $response->withStatus(404);

            return $this->responseWithData($request, $response, [
                'error' => [
                    'message' => sprintf('Group [%d] not found', $id)
                ]
            ]);
        }

        if (!$groupService->canDelete($id)) {
            $response = $response->withStatus(403);

            return $this->responseWithData($request, $response, [
                'error' => [
                    'message' => sprintf('You are not allowed to delete group [%s]', $group->name)
                ]
            ]);
        }

        $tableGateway = $groupService->getTableGateway();
        $tableGateway->delete(['id' => $id]);

        return $this->responseWithData($request, $response, []);
    }
}
