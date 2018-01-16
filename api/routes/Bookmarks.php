<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusBookmarksTableGateway;
use Directus\Database\TableGateway\DirectusPreferencesTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Exception\UnauthorizedException;
use Directus\Permissions\Acl;
use Directus\Util\ArrayUtils;

class Bookmarks extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'one']);
        $app->patch('/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']);
        $app->get('/user/me', [$this, 'mine']);
        $app->get('/user/{id}', [$this, 'user']);
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
        $tableGateway = new RelationalTableGateway('directus_bookmarks', $dbConnection, $acl);

        $params = $request->getQueryParams();

        $data = $this->getEntriesAndSetResponseCacheTags($tableGateway, $params);

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        return $this->update($request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws UnauthorizedException
     */
    public function update(Request $request, Response $response)
    {
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $payload = $request->getParsedBody();
        $id = $request->getAttribute('id');

        $currentUserId = $acl->getUserId();
        $bookmarks = new DirectusBookmarksTableGateway($dbConnection, $acl);
        $preferences = new DirectusPreferencesTableGateway($dbConnection, null);

        if (!$request->isDelete()) {
            $this->validateRequestWithTable($request, 'directus_bookmarks');
        }

        switch ($request->getMethod()) {
            case 'PATCH':
                $bookmarks->updateBookmark($payload);
                break;
            case 'POST':
                $payload['user'] = ArrayUtils::get($payload, 'user', $currentUserId);
                $id = $bookmarks->insertBookmark($payload);
                break;
            case 'DELETE':
                $bookmark = $bookmarks->fetchEntityByUserAndId($currentUserId, $id);
                $responseData = [];

                if (!empty($bookmark['data'])) {
                    $bookmarks->delete(['id' => $id]);

                    // delete the preferences
                    $preferences->delete([
                        'user' => $currentUserId,
                        'title' => $bookmark['data']['title']
                    ]);
                } else {
                    $responseData = [
                        'error' => [
                            'message' => __t('bookmark_not_found')
                        ]
                    ];
                }

                return $this->responseWithData($request, $response, $responseData);
        }

        $params = ['meta' => $request->getQueryParam('meta')];
        if (!is_null($id)) {
            $params['id'] = $id;
        }

        $responseData = $this->getEntriesAndSetResponseCacheTags($bookmarks, $params);

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
        $id = $request->getAttribute('id');
        $params = $request->getQueryParams();
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        if (!is_null($id)) {
            $params['id'] = $id;
        }

        $bookmarks = new DirectusBookmarksTableGateway($dbConnection, $acl);
        $responseData = $this->getEntriesAndSetResponseCacheTags($bookmarks, $params);

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
        return $this->update($request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function user(Request $request, Response $response)
    {
        $id = $request->getAttribute('id');

        return $this->processUserBookmarks($id, $request, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function mine(Request $request, Response $response)
    {
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        $id = $acl->getUserId();

        return $this->processUserBookmarks($id, $request, $response);
    }

    /**
     * @param int $userId
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function processUserBookmarks($userId, Request $request, Response $response)
    {
        $acl = $this->container->get('acl');
        $dbConnection = $this->container->get('database');
        $params = $request->getQueryParams();

        $bookmarks = new DirectusBookmarksTableGateway($dbConnection, $acl);
        $params['filter'] = ['user' => $userId];
        $responseData = $this->getDataAndSetResponseCacheTags([$bookmarks, 'getEntries'], [$params]);

        return $this->responseWithData($request, $response, $responseData);
    }
}
