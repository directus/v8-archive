<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Exception\UnauthorizedException;
use Directus\Services\TablesService;
use Directus\Util\ArrayUtils;

class Collections extends Route
{
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{name}', [$this, 'read']);
        $app->patch('/{name}', [$this, 'update']);
        $app->delete('/{name}', [$this, 'delete']);
    }

    /**
     * @throws \Exception
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $tableService = new TablesService($this->container);
        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();
        $name = ArrayUtils::get($payload, 'collection');
        $data = ArrayUtils::omit($payload, 'collection');
        $responseData = $tableService->createTable($name, $data, $params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $service = new TablesService($this->container);
        $responseData = $service->findAll($params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        $service = new TablesService($this->container);
        $responseData = $service->findByIds(
            $request->getAttribute('name'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @throws UnauthorizedException
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        $service = new TablesService($this->container);
        $responseData = $service->updateTable(
            $request->getAttribute('name'),
            $request->getParsedBody() ?: [],
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $service = new TablesService($this->container);
        $service->delete(
            $request->getAttribute('name'),
            $request->getQueryParams()
        );

        $response = $response->withStatus(204);

        return $this->responseWithData($request, $response, []);
    }
}
