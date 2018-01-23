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
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'one']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
        $app->get('', [$this, 'all']);
        // $app->map(['GET', 'PATCH', 'POST', 'PUT', 'DELETE'], '[/{id}]', [$this, 'all']);

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
    public function create(Request $request, Response $response)
    {
        $table = 'directus_files';
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $payload = $request->getParsedBody();
        $params = $request->getParams();
        $filesTableGateway = new RelationalTableGateway($table, $dbConnection, $acl);

        $payload['user'] = $acl->getUserId();
        $payload['date_uploaded'] = DateUtils::now();

        $validationConstraints = $this->createConstraintFor($table);
        $this->validate($payload, array_merge(['data' => 'required'], $validationConstraints));

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
        $newFile = $filesTableGateway->updateRecord($payload, $this->getActivityMode());

        $responseData = $filesTableGateway->wrapData(
            $newFile->toArray(),
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
        $params = ArrayUtils::pick($request->getParams(), ['fields', 'meta']);
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $filesTableGateway = new RelationalTableGateway('directus_files', $dbConnection, $acl);

        $params['id'] = $request->getAttribute('id');
        $responseData = $this->getEntriesAndSetResponseCacheTags($filesTableGateway, $params);

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
        $table = 'directus_files';
        $this->validateRequestWithTable($request, $table);

        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $payload = $request->getParsedBody();
        $params = $request->getParams();
        $filesTableGateway = new RelationalTableGateway($table, $dbConnection, $acl);

        $payload['id'] = $request->getAttribute('id');
        $newFile = $filesTableGateway->updateRecord($payload, $this->getActivityMode());

        $responseData = $filesTableGateway->wrapData(
            $newFile->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    public function delete(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $filesTableGateway = new RelationalTableGateway('directus_files', $dbConnection, $acl);

        $id = $request->getAttribute('id');
        $file = $filesTableGateway->loadItems(['id' => $id]);

        // Force delete files
        // TODO: Make the hook listen to deletes and catch ALL ids (from conditions)
        // and deletes every matched files
        $filesTableGateway->deleteFile($id);

        // Delete file record
        $filesTableGateway->delete([
            $filesTableGateway->primaryKeyFieldName => $id
        ]);

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
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $params = $request->getParams();

        $table = 'directus_files';
        $filesTableGateway = new RelationalTableGateway($table, $dbConnection, $acl);
        $responseData = $this->getEntriesAndSetResponseCacheTags($filesTableGateway, $params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function upload(Request $request, Response $response)
    {
        $Files = $this->container->get('files');
        $result = [];

        foreach ($_FILES as $file) {
            $result[] = $Files->upload($file);
        }

        $responseData = [
            'data' => $result
        ];

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function uploadLink(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $Files = $this->container->get('files');
        $link = $request->getParam('link');

        $responseData = [
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

                $items = [];
                $items[] = [
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

                $responseData = ['data' => $items];
            }
        }

        return $this->responseWithData($request, $response, $responseData);
    }
}
