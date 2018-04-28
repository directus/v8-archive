<?php

namespace Directus\Application;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Hook\Emitter;
use Directus\Util\ArrayUtils;
use Directus\Validator\Validator;

abstract class Route
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Convert array data into an API output format
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @param array $options
     *
     * @return Response
     */
    public function responseWithData(Request $request, Response $response, array $data, array $options = [])
    {
        $data = $this->triggerResponseFilter($request, $data, (array) $options);

        // TODO: Response will support xml

        // NOTE: when data is a empty array, the output will be an array
        // this create problem/confusion as we always return an object
        if (empty($data)) {
            $data = new \stdClass();
        }

        return $response->withJson($data);
    }

    /**
     * Trigger a response filter
     *
     * @param Request $request
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    protected function triggerResponseFilter(Request $request, array $data, array $options = [])
    {
        $meta = ArrayUtils::get($data, 'meta');
        $method = $request->getMethod();

        $attributes = [
            'meta' => $meta,
            'request' => [
                'path' => $request->getUri()->getPath(),
                'method' => $method
            ]
        ];

        /** @var Emitter $emitter */
        $emitter = $this->container->get('hook_emitter');

        $payload = $emitter->apply('response', $data, $attributes);
        $payload = $emitter->apply('response.' . $method, $payload);

        if (isset($meta['table'])) {
            $emitter->apply('response.' . $meta['table'], $payload);
            $payload = $emitter->apply(sprintf('response.%s.%s',
                $meta['table'],
                $method
            ), $payload);
        }

        return $payload;
    }

    /**
     * Parse the output data
     *
     * @param Response $response
     * @param array $data
     * @param array $options
     *
     * @return Response
     */
    public function withData(Response $response, array $data, array $options = [])
    {
        // TODO: Event parsing output
        // This event can guess/change the output from json to xml

        return $response->withJson($data);
    }
}
