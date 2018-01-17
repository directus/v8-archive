<?php

/**
 * @param string $method
 * @param string $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request($method, $path, array $options = [])
{
    $http = new GuzzleHttp\Client([
        'base_uri' => 'http://directus.local:8888/api/'//'http://localhost/api/'
    ]);

    $response = $http->request($method, $path, $options);

    return $response;
}

/**
 * @param string $path
 * @param array $params
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_get($path, array $params = [], array $options = [])
{
    $options['query'] = $params;

    return request('GET', $path, $options);
}

/**
 * @param string $path
 * @param array $body
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_post($path, array $body = [], array $options = [])
{
    $options['form_params'] = $body;

    return request('POST', $path, $options);
}

/**
 * @param string $path
 * @param array $body
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_patch($path, array $body = [], array $options = [])
{
    $options['form_params'] = $body;

    return request('PATCH', $path, $options);
}

/**
 * @param string $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_delete($path, array $options = [])
{
    return request('DELETE', $path, $options);
}

/**
 * @param string $method
 * @param string $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error($method, $path, array $options = [])
{
    try {
        $response = request($method, $path, $options);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
    }

    return $response;
}

/**
 * @param $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error_get($path, array $options = [])
{
    return request_error('GET', $path, $options);
}

/**
 * @param $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error_post($path, array $options = [])
{
    return request_error('POST', $path, $options);
}

/**
 * @param $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error_put($path, array $options = [])
{
    return request_error('PUT', $path, $options);
}

/**
 * @param $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error_patch($path, array $options = [])
{
    return request_error('PATCH', $path, $options);
}

/**
 * @param $path
 * @param array $options
 *
 * @return \Psr\Http\Message\ResponseInterface
 */
function request_error_delete($path, array $options = [])
{
    return request_error('DELETE', $path, $options);
}