<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;

class Files extends Route
{
    use ActivityMode;

    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->map(['GET', 'PATCH', 'POST', 'PUT', 'DELETE'], '[/{id}]', [$this, 'all']);

        // TODO: This is breaking the above path format, upload should be perform
        // $app->post('/upload', [$this, 'upload']);
        // $app->post('/upload/link', [$this, 'uploadLink']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function all(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $payload = $request->getParsedBody();
        $params = $request->getParams();
        $id = $request->getAttribute('id');

        if (!is_null($id)) {
            $params['id'] = $id;
        }

        $table = 'directus_files';
        $TableGateway = new RelationalTableGateway($table, $dbConnection, $acl);

        switch ($request->getMethod()) {
            case 'DELETE':
                // Force delete files
                $payload['id'] = $id;

                // Deletes files
                // TODO: Make the hook listen to deletes and catch ALL ids (from conditions)
                // and deletes every matched files
                $TableGateway->deleteFile($id);

                $conditions = [
                    $TableGateway->primaryKeyFieldName => $id
                ];

                // Delete file record
                $success = $TableGateway->delete($conditions);

                $data = [];
                if (!$success) {
                    $data = [
                        'error' => 'Failed deleting id: ' . $id
                    ];
                }

                return $this->withData($response, $data);

                break;
            case 'POST':
                $payload['user'] = $acl->getUserId();
                $payload['date_uploaded'] = DateUtils::now();

                if (!ArrayUtils::has($payload, 'name')) {
                    return $this->withData($response, [
                        'error' => [
                            'message' => __t('upload_missing_filename')
                        ]
                    ]);
                }

                // When the file is uploaded there's not a data key
                if (array_key_exists('data', $payload)) {
                    $Files = $this->container->get('files');
                    $dataInfo = $Files->getDataInfo($payload['data']);
                    $type = ArrayUtils::get($dataInfo, 'type', ArrayUtils::get($payload, 'type'));

                    if (!$type) {
                        return $this->withData($response, [
                            'error' => [
                                'message' => __t('upload_missing_file_type')
                            ]
                        ]);
                    }

                    if (strpos($type, 'embed/') === 0) {
                        $recordData = $Files->saveEmbedData($payload);
                    } else {
                        $recordData = $Files->saveData($payload['data'], $payload['name']);
                    }

                    $payload = array_merge($recordData, ArrayUtils::omit($payload, ['data', 'name']));
                }
                $newRecord = $TableGateway->updateRecord($payload, $this->getActivityMode());
                $params['id'] = $newRecord['id'];
                break;
            case 'PATCH':
            case 'PUT':
                $payload['id'] = $id;
                if (!is_null($id)) {
                    $TableGateway->updateRecord($payload, $this->getActivityMode());
                    break;
                }
        }

        $Files = new RelationalTableGateway($table, $dbConnection, $acl);
        $data = $this->getEntriesAndSetResponseCacheTags($Files, $params);
        if (!$data) {
            $data = [
                'error' => [
                    'message' => __t('unable_to_find_file_with_id_x', ['id' => $id])
                ]
            ];
        }

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Files
     */
    protected function upload(Request $request, Response $response)
    {
        $Files = $this->container->get('files');
        $result = [];

        foreach ($_FILES as $file) {
            $result[] = $Files->upload($file);
        }

        return $this-$this->withData($response, $result);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function uploadLink(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $Files = $this->container->get('files');
        $link = $request->getParam('link');

        $result = [
            'error' => [
                'message' => __t('invalid_unsupported_url')
            ],
        ];

        $response = $response->withStatus(400);

        if (isset($link) && filter_var($link, FILTER_VALIDATE_URL)) {
            $fileData = ['caption' => '', 'tags' => '', 'location' => ''];
            $linkInfo = $Files->getLink($link);

            if ($linkInfo) {
                $currentUserId = $acl->getUserId();
                $response = $response->withStatus(200);
                $fileData = array_merge($fileData, $linkInfo);

                $result = [];
                $result[] = [
                    'type' => $fileData['type'],
                    'name' => $fileData['name'],
                    'title' => $fileData['title'],
                    'tags' => $fileData['tags'],
                    'caption' => $fileData['caption'],
                    'location' => $fileData['location'],
                    'charset' => $fileData['charset'],
                    'size' => $fileData['size'],
                    'width' => $fileData['width'],
                    'height' => $fileData['height'],
                    'html' => isset($fileData['html']) ? $fileData['html'] : null,
                    'embed_id' => (isset($fileData['embed_id'])) ? $fileData['embed_id'] : '',
                    'data' => (isset($fileData['data'])) ? $fileData['data'] : null,
                    'user' => $currentUserId
                    //'date_uploaded' => $fileData['date_uploaded'] . ' UTC',
                ];
            }
        }

        return $this->withData($response, $result);
    }
}
