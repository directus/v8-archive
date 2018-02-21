<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Hash\HashManager;
use Directus\Util\StringUtils;

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
        $string = $request->getParam('string');
        $hasher = $request->getParam('hasher', 'core');
        $options = $request->getParam('options', []);

        $this->validate(['string' => $string], ['string' => 'required|string']);

        if (!is_array($options)) {
            $options = [$options];
        }

        $options['hasher'] = $hasher;
        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('hash_manager');
        $hashedString = $hashManager->hash($string, $options);

        return $this->responseWithData($request, $response, [
            'data' => [
                'hash' => $hashedString
            ]
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function randomString(Request $request, Response $response)
    {
        // TODO: Create a service/function that shared the same code with other part of Directus
        // default random string length
        $length = $request->getParam('length', 32);

        $this->validate(['length' => $length], ['length' => 'numeric']);

        $randomString = StringUtils::randomString($length);

        return $this->responseWithData($request, $response, [
            'data' => [
                'random' => $randomString
            ]
        ]);
    }
}
