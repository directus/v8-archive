<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\UtilsService;

class Utils extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/hash', [$this, 'hash']);
        $app->post('/random_string', [$this, 'randomString']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function hash(Request $request, Response $response)
    {
        $service = new UtilsService($this->container);

        $options = $request->getParam('options', []);
        if (!is_array($options)) {
            $options = [$options];
        }

        $responseData = $service->hashString(
            $request->getParam('string'),
            $request->getParam('hasher', 'core'),
            $options
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function randomString(Request $request, Response $response)
    {
        $service = new UtilsService($this->container);
        $responseData = $service->randomString(
            $request->getParsedBodyParam('length', 32),
            $request->getParsedBodyParam('options')
        );

        return $this->responseWithData($request, $response, $responseData);
    }
}
