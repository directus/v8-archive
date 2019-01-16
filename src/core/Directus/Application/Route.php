<?php

namespace Directus\Application;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Exception\BadRequestException;
use Directus\Exception\InvalidPayloadException;
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
        $data = $this->getResponseData($request, $response, $data, $options);

        $this->triggerResponseAction($request, $response, $data);

        // TODO: Ideally here we should check if the response is a empty response and return 204 not content
        return $response->withJson($data);
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
    public function responseScimWithData(Request $request, Response $response, array $data, array $options = [])
    {
        $data = $this->getResponseData($request, $response, $data, $options);

        return $response->withScimJson($data);
    }

    /**
     * Pass the data through response hook filters
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @param array $options
     *
     * @return array|mixed|\stdClass
     */
    protected function getResponseData(Request $request, Response $response, array $data, array $options = [])
    {
        $data = $this->triggerResponseFilter($request, $data, (array) $options);

        // NOTE: when data is a empty array, the output will be an array
        // this create problem/confusion as we always return an object
        if (empty($data)) {
            $data = new \stdClass();
        }

        return $data;
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
     * Trigger a response action
     * @param  Request  $request
     * @param  Response $response
     * @return void
     */
    protected function triggerResponseAction(Request $request, Response $response, array $data) {
        $uri = $request->getUri();

        $responseInfo = [
            'path' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'status' => $response->getStatusCode(),
            'method' => $request->getMethod(),

            // This will count the total byte length of the data. It isn't
            // 100% accurate, as it will count the size of the serialized PHP
            // array instead of the JSON object. Converting it to JSON before
            // counting would introduce too much latency and the difference in
            // length between the JSON and PHP array is insignificant
            'size' => mb_strlen(serialize((array) $data), '8bit')
        ];

        $hookEmitter = $this->container->get('hook_emitter');
        $hookEmitter->run("response", [$responseInfo, $data]);
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

        $this->triggerResponseAction($request, $response, $data);

        return $response->withJson($data);
    }

    /**
     * Throws exception when request payload is invalid
     *
     * @param Request $request
     *
     * @throws BadRequestException
     */
    public function validateRequestPayload(Request $request)
    {
        $payload = $request->getParsedBody();
        if ($payload === null) {
            throw new InvalidPayloadException();
        }
    }
}
