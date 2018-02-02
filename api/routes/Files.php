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
        $app->get('/{id:[0-9]+}', [$this, 'one']);
        $app->patch('/{id:[0-9]+}', [$this, 'update']);
        $app->delete('/{id:[0-9]+}', [$this, 'delete']);
        $app->get('', [$this, 'all']);

        // Folders
        $controller = $this;
        $app->group('/folders', function () use ($controller) {
            $this->post('', [$controller, 'createFolder']);
            $this->get('/{id:[0-9]+}', [$controller, 'oneFolder']);
            $this->patch('/{id:[0-9]+}', [$controller, 'updateFolder']);
            $this->delete('/{id:[0-9]+}', [$controller, 'deleteFolder']);
            $this->get('', [$controller, 'allFolder']);
        });
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

        $payload['upload_user'] = $acl->getUserId();
        $payload['upload_date'] = DateUtils::now();

        $validationConstraints = $this->createConstraintFor($table);
        $this->validate($payload, array_merge(['data' => 'required'], $validationConstraints));

        /** @var \Directus\Filesystem\Files $Files */
        $Files = $this->container->get('files');

        if (array_key_exists('data', $payload) && filter_var($payload['data'], FILTER_VALIDATE_URL)) {
            $dataInfo = $Files->getLink($payload['data']);
        } else {
            $dataInfo = $Files->getDataInfo($payload['data']);
        }

        $type = ArrayUtils::get($dataInfo, 'type', ArrayUtils::get($payload, 'type'));

        if (strpos($type, 'embed/') === 0) {
            $recordData = $Files->saveEmbedData($dataInfo);
        } else {
            $recordData = $Files->saveData($payload['data'], $payload['filename']);
        }

        $payload = array_merge($recordData, ArrayUtils::omit($payload, ['data', 'filename']));
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
    public function createFolder(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');

        $this->validateRequestWithTable($request, 'directus_folders');
        $foldersTableGateway = new RelationalTableGateway('directus_folders', $dbConnection, $acl);

        $newFolder = $foldersTableGateway->updateRecord($payload);
        $responseData = $foldersTableGateway->wrapData(
            $newFolder->toArray(),
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
    public function oneFolder(Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $foldersTableGateway = new RelationalTableGateway('directus_folders', $dbConnection, $acl);

        $params = ArrayUtils::pick($request->getQueryParams(), ['fields', 'meta']);
        $params['id'] = $request->getAttribute('id');
        $responseData = $this->getEntriesAndSetResponseCacheTags($foldersTableGateway, $params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function updateFolder(Request $request, Response $response)
    {
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $foldersTableGateway = new RelationalTableGateway('directus_folders', $dbConnection, $acl);

        $payload['id'] = $request->getAttribute('id');
        $group = $foldersTableGateway->updateRecord($payload);

        $responseData = $foldersTableGateway->wrapData(
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
    public function allFolder(Request $request, Response $response)
    {
        $container = $this->container;
        $acl = $container->get('acl');
        $dbConnection = $container->get('database');
        $params = $request->getQueryParams();

        $foldersTableGateway = new RelationalTableGateway('directus_folders', $dbConnection, $acl);
        $responseData = $this->getEntriesAndSetResponseCacheTags($foldersTableGateway, $params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteFolder(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');

        $foldersTableGateway = new RelationalTableGateway('directus_folders', $dbConnection, $acl);
        $this->getEntriesAndSetResponseCacheTags($foldersTableGateway, [
            'id' => $id
        ]);

        $foldersTableGateway->delete(['id' => $id]);

        return $this->responseWithData($request, $response, []);
    }
}
