<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusActivityTableGateway;

class Revisions extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('/{table}/{id}', [$this, 'all']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        $params = $request->getQueryParams();
        $tableName = $request->getAttribute('table');
        $id = $request->getAttribute('id');

        $params['table_name'] = $tableName;
        $params['id'] = $id;

        $Activity = new DirectusActivityTableGateway($dbConnection, $acl);

        $revisions = $this->getDataAndSetResponseCacheTags(
            [$Activity, 'fetchRevisions'],
            [$id, $tableName]
        );

        return $this->responseWithData($request, $response, $revisions);
    }
}
