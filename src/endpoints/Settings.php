<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusSettingsTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Util\ArrayUtils;

class Settings extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('', [$this, 'create']);
        $app->get('', [$this, 'all']);
        $app->get('/{id}', [$this, 'one']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $this->validateRequestWithTable($request, 'directus_settings');

        $params = $request->getQueryParams();
        $payload = $request->getParsedBody();
        $settingsTable = $this->getTableGateway();
        $newRecord = $settingsTable->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_PARENT);

        $responseData = $settingsTable->wrapData(
            $newRecord->toArray(),
            false,
            ArrayUtils::get($params, 'meta', 0)
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
        $settingTable = $this->getTableGateway();
        $params = $request->getParams();

        // TODO: Support uploading image
        $responseData = $this->getEntriesAndSetResponseCacheTags($settingTable, $params);

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
        $params = $request->getQueryParams();
        $settingsTable = $this->getTableGateway();

        $params['id'] = $request->getAttribute('id');
        $responseData = $settingsTable->getItems($params);

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
        $this->validateRequestWithTable($request, 'directus_settings');

        $params = $request->getQueryParams();
        $payload = $request->getParsedBody();
        $settingsTable = $this->getTableGateway();

        $payload['id'] = $request->getAttribute('id');
        $newRecord = $settingsTable->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_PARENT);

        $responseData = $settingsTable->wrapData(
            $newRecord->toArray(),
            false,
            ArrayUtils::get($params, 'meta', 0)
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
        $settingsTable = $this->getTableGateway();
        $settingsTable->delete(['id' => $id]);

        return $this->responseWithData($request, $response, []);
    }

    /**
     * @return DirectusSettingsTableGateway
     */
    public function getTableGateway()
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');

        return new DirectusSettingsTableGateway($dbConnection, $acl);
    }
}
