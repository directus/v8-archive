<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Hash\HashManager;
use Directus\Util\StringUtils;
use Slim\Http\Request;

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
    protected function hash(Request $request, Response $response)
    {
        $string = $request->getParam('string');
        $hasher = $request->getParam('hasher', 'core');
        $options = $request->getParam('options', []);

        if (!is_array($options)) {
            $options = [$options];
        }

        if (empty($string)) {
            return $this->withData($response, [
                'error' => [
                    'message' => __t('hash_expect_a_string')
                ]
            ]);
        }

        $options['hasher'] = $hasher;
        /** @var HashManager $hashManager */
        $hashManager = $this->container->get('hash_manager');
        $hashedString = $hashManager->hash($string, $options);

        return $this->withData($response, [
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
    protected function randomString(Request $request, Response $response)
    {
        // TODO: Create a service/function that shared the same code with other part of Directus
        // default random string length
        $length = (int)$request->getParam('length', 32);
        $randomString = StringUtils::randomString($length);

        return $this->withData($response, [
            'data' => [
                'random' => $randomString
            ]
        ]);
    }
}
