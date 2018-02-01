<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusPermissionsTableGateway;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Exception\Http\ForbiddenException;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;

class Permissions extends Route
{
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
        $this->validateRequestWithTable($request, 'directus_permissions');

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $groupsTableGateway = new DirectusPermissionsTableGateway($dbConnection, $acl);

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
        $permissionsTableGateway = new DirectusPermissionsTableGateway($dbConnection, $acl);

        $params = ArrayUtils::pick($request->getQueryParams(), ['fields', 'meta']);
        $params['id'] = $request->getAttribute('id');
        $responseData = $this->getEntriesAndSetResponseCacheTags($permissionsTableGateway, $params);

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
        $this->validateRequestWithTable($request, 'directus_permissions');

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $permissionsTableGateway = new DirectusPermissionsTableGateway($dbConnection, $acl);

        $payload['id'] = $request->getAttribute('id');
        $newGroup = $permissionsTableGateway->updateRecord($payload);
        $responseData = $permissionsTableGateway->wrapData(
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
    public function delete(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');

        $permissionsTableGateway = new DirectusPermissionsTableGateway($dbConnection, $acl);
        $this->getEntriesAndSetResponseCacheTags($permissionsTableGateway, [
            'id' => $id
        ]);

        $permissionsTableGateway->delete(['id' => $id]);

        return $this->responseWithData($request, $response, []);
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

        $permissionsTableGateway = new DirectusPermissionsTableGateway($dbConnection, $acl);
        $responseData = $this->getEntriesAndSetResponseCacheTags($permissionsTableGateway, $params);

        return $this->responseWithData($request, $response, $responseData);
    }
}
