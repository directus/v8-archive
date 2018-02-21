<?php

namespace Directus\Api\Routes;

use Directus\Api\Services\PreferencesService;
use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\TableGateway\DirectusCollectionPresetsTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use Zend\Db\RowGateway\RowGateway;

class CollectionPresets extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'one']);
        $app->put('/{id}', [$this, 'replace']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
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
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $preferencesTableGateway = new DirectusCollectionPresetsTableGateway($dbConnection, $acl);

        $responseData = $this->getDataAndSetResponseCacheTags(
            [$preferencesTableGateway, 'getItems'],
            [$params]
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
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $params = ArrayUtils::pick($request->getQueryParams(), ['meta', 'fields']);
        $params['id'] = $request->getAttribute('id');
        $preferencesTableGateway = new DirectusCollectionPresetsTableGateway($dbConnection, $acl);

        $responseData = $this->getDataAndSetResponseCacheTags(
            [$preferencesTableGateway, 'getItems'],
            [$params]
        );

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
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        // TODO: Throw an exception if ID exist in payload
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        if (!ArrayUtils::has($payload, 'user')) {
            $payload['user'] = $acl->getUserId();;
        }

        $preferencesTableGateway = new DirectusCollectionPresetsTableGateway($dbConnection, $acl);
        /** @var RowGateway $preference */
        $preference = $preferencesTableGateway->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);
        $responseData = $preferencesTableGateway->wrapData($preference->toArray(), true, ArrayUtils::get($params, 'meta'));

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
        $id = $request->getAttribute('id');
        $params = $request->getQueryParams();
        // TODO: Throw an exception if ID in payload is different than $id (attribute)
        $payload = $request->getParsedBody();
        $payload['id'] = $id;

        $preferencesTableGateway = new DirectusCollectionPresetsTableGateway($dbConnection, $acl);
        /** @var RowGateway $preference */
        $preference = $preferencesTableGateway->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);
        $responseData = $preferencesTableGateway->wrapData($preference->toArray(), true, ArrayUtils::get($params, 'meta'));

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function replace(Request $request, Response $response)
    {
        return $response->withStatus(404);
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
        $id = $request->getAttribute('id');

        $preferencesTableGateway = new DirectusCollectionPresetsTableGateway($dbConnection, $acl);

        // TODO: throw exception if ID is not integer
        // TODO: throw exception if fail
        $ok = $preferencesTableGateway->delete(['id' => $id]);

        if ($ok) {
            $response = $response->withStatus(204);
        }

        return $this->responseWithData($request, $response, []);
    }
}
