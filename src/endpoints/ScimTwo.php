<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Services\ScimService;
use Directus\Util\ArrayUtils;

class ScimTwo extends Route
{
    /**
     * @var ScimService
     */
    protected $service;

    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/Users', [$this, 'createUser'])->setName('scim_v2_create_user');
        $app->get('/Users/{id}', [$this, 'oneUser'])->setName('scim_v2_read_user');
        // $app->patch('/Users/{id}', [$this, 'updateUser'])->setName('scim_v2_update_user');
        $app->get('/Users', [$this, 'listUsers'])->setName('scim_v2_list_users');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function createUser(Request $request, Response $response)
    {
        $service = $this->getService();

        $responseData = $service->create(
            $request->getParsedBody()
        );

        return $this->responseScimWithData($request, $response, $this->parseUserData($responseData));
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function oneUser(Request $request, Response $response)
    {
        $service = $this->getService();

        $responseData = $service->find(
            $request->getAttribute('id')
        );

        return $this->responseScimWithData(
            $request,
            $response,
            $responseData
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function listUsers(Request $request, Response $response)
    {
        $responseData = $this->getService()->findAll($request->getQueryParams());

        return $this->responseScimWithData(
            $request,
            $response,
            $responseData
        );
    }

    /**
     * Gets the users service
     *
     * @return ScimService
     */
    protected function getService()
    {
        if (!$this->service) {
            $this->service = new ScimService($this->container);
        }

        return $this->service;
    }
}
