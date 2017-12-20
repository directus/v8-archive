<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusPreferencesTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;

class Preferences extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/{table}', [$this, 'all']);
        // TODO: Per user preferences. Getting the preference of an user's table
        // NOTE: Should we add the saved information omitting the default values
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $currentUserId = $acl->getUserId();
        $tableName = $request->getAttribute('table');

        $params['table_name'] = $tableName;
        $Preferences = new DirectusPreferencesTableGateway($dbConnection, $acl);
        $TableGateway = new RelationalTableGateway('directus_preferences', $dbConnection, $acl);
        switch ($request->getMethod()) {
            case 'PUT':
                $TableGateway->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);
                break;
            case 'POST':
                //If Already exists and not saving with title, then update it!
                $existing = $Preferences->fetchByUserAndTableAndTitle(
                    $currentUserId,
                    $tableName,
                    isset($payload['title']) ? $payload['title'] : null
                );
                if (!empty($existing)) {
                    $payload['id'] = $existing['id'];
                }
                $payload['user'] = $currentUserId;
                $id = $TableGateway->updateRecord($payload, RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED);
                break;
            case 'DELETE':
                if ($payload['user'] != $currentUserId) {
                    return $response;
                }

                if (isset($payload['id'])) {
                    echo $TableGateway->delete(['id' => $payload['id']]);
                } else if (isset($payload['title']) && isset($payload['table_name'])) {
                    $jsonResponse = $Preferences->fetchByUserAndTableAndTitle($currentUserId, $payload['table_name'], $payload['title']);
                    if ($jsonResponse['id']) {
                        echo $TableGateway->delete(['id' => $jsonResponse['id']]);
                    } else {
                        echo 1;
                    }
                }

                return $response;
        }

        // If Title is set then return this version
        // this is the bookmark title
        $title = $request->getParam('title');
        $fields = $request->getQueryParam('fields', []);
        if (!is_array($fields)) {
            $fields = array_filter(StringUtils::csv($fields));
        }

        $preferences = $this->getDataAndSetResponseCacheTags(
            [$Preferences, 'fetchByUserAndTableAndTitle'],
            [$currentUserId, $tableName, $title, $fields]
        );

        if (!$preferences) {
            // @TODO: The app treat 404 as not found url, instead of not found resource
            // $app->response()->setStatus(404);
            $jsonResponse = [
                'error' => [
                    'message' => __t('unable_to_find_preferences')
                ]
            ];
        } else {
            $jsonResponse = [];

            if (ArrayUtils::get($params, 'meta', 0) == 1) {
                $jsonResponse['meta'] = [
                    'type' => 'item',
                    'table' => 'directus_preferences'
                ];
            }

            $jsonResponse['data'] = $preferences;
        }

        return $this->withData($response, $jsonResponse);
    }
}
