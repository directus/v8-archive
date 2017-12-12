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
        $app->map(['GET', 'PATCH', 'POST', 'PUT'], '[/{id}]', [$this, 'all']);
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
        $params = $request->getParams();
        $id = $request->getAttribute('id');

        $Settings = new DirectusSettingsTableGateway($dbConnection, $acl);

        switch ($request->getMethod()) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                $data = $payload;
                $insertSettings = function ($values, $collection = null) use ($Settings, $container) {
                    $Files = $container->get('files');

                    foreach ($values as $key => $value) {
                        $isLogo = $key === 'cms_thumbnail_url';
                        if (!$isLogo || !is_array($value)) {
                            continue;
                        }

                        if (isset($value['data'])) {
                            $data = ArrayUtils::get($value, 'data', '');
                            $name = ArrayUtils::get($value, 'name', 'unknown.jpg');
                            $fileData = $Files->saveData($data, $name);
                            $newRecord = $Settings->manageRecordUpdate('directus_files', $fileData, RelationalTableGateway::ACTIVITY_ENTRY_MODE_PARENT);

                            $values[$key] = $newRecord['id'];
                        } else {
                            $values[$key] = ArrayUtils::get($value, 'id');
                        }

                        break;
                    }

                    $Settings->setValues($values, $collection);
                };
                if (!is_null($id)) {
                    $insertSettings($data);
                } else {
                    foreach ($data as $collection => $values) {
                        $insertSettings($values, $collection);
                    }
                }
                break;
        }

        $fetchCmsFile = function ($data) use ($dbConnection, $acl) {
            if (!$data) {
                return $data;
            }

            foreach ($data as $key => $value) {
                if ($key === 'cms_thumbnail_url') {
                    $filesTableGateway = new RelationalTableGateway('directus_files', $dbConnection, $acl);
                    $data[$key] = $filesTableGateway->loadEntries(['id' => $value]);
                    break;
                }
            }

            return $data;
        };

        if (!is_null($id)) {
            $data = $this->getDataAndSetResponseCacheTags(
                [$Settings, 'fetchCollection'],
                [$id]
            );
        } else {
            $data = $this->getDataAndSetResponseCacheTags([$Settings, 'fetchAll']);
            $data['global'] = $fetchCmsFile($data['global']);
        }

        if (!$data) {
            $data = [
                'error' => [
                    'message' => __t('unable_to_find_setting_collection_x', ['collection' => $id])
                ]
            ];
        } else {
            $data = [
                'meta' => [
                    'type' => 'item',
                    'table' => 'directus_settings'
                ],
                'data' => $data
            ];

            if (!is_null($id)) {
                $data['meta']['settings_collection'] = $id;
            }
        }

        if (ArrayUtils::get($params, 'meta', 0) == 1) {
            unset($data['meta']);
        }

        return $this->withData($response, $data);
    }
}
