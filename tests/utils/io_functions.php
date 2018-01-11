<?php

function create_db_connection()
{
    $charset = 'utf8mb4';

    return new \Directus\Database\Connection([
        'driver' => 'Pdo_mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'directus',
        'username' => 'root',
        'password' => null,
        'charset' => $charset,
        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        \PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES "%s"', $charset)
    ]);
}
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

/**
 * @param \Psr\Http\Message\ResponseInterface $response
 *
 * @return object
 */
function response_to_json(\Psr\Http\Message\ResponseInterface $response)
{
    // rewind the pointer to the beginning
    // after getting the content, you must rewind or the content is a empty
    $response->getBody()->rewind();

    return json_decode($response->getBody()->getContents());
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param array $options
 */
function response_assert(PHPUnit_Framework_TestCase $testCase, \Psr\Http\Message\ResponseInterface $response, array $options = [])
{
    $result = response_to_json($response);

    $testCase->assertObjectHasAttribute('data', $result);
    $testCase->assertObjectNotHasAttribute('error', $result);

    $dataType = \Directus\Util\ArrayUtils::get($options, 'data') === 'array' ? 'array' : 'object';
    $testCase->assertInternalType($dataType, $result->data);

    if (\Directus\Util\ArrayUtils::get($options, 'public', false)) {
        $testCase->assertObjectHasAttribute('public', $result);
    } else {
        $testCase->assertObjectNotHasAttribute('public', $result);
    }

    if (isset($options['count'])) {
        $testCase->assertCount($options['count'], $result->data);
    }

    if (isset($options['status'])) {
        $testCase->assertSame($options['status'], $response->getStatusCode());
    }

    if (isset($options['fields'])) {
        $data = $result->data;
        $fields = $options['fields'];

        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                $testCase->assertTrue(in_array($key, $fields), $key);
            }
        }
    }
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param array $options
 */
function response_assert_meta(PHPUnit_Framework_TestCase $testCase, \Psr\Http\Message\ResponseInterface $response, array $options)
{
    $result = response_to_json($response);
    $testCase->assertObjectHasAttribute('meta', $result);
    $testCase->assertObjectNotHasAttribute('error', $result);
    $meta = $result->meta;
    $validOptions = ['table', 'type', 'result_count'];

    foreach ($validOptions as $option) {
        if (isset($options[$option])) {
            $testCase->assertObjectHasAttribute($option, $meta);
            $testCase->assertSame($options[$option], $meta->{$option});
        }
    }
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param \Psr\Http\Message\ResponseInterface $response
 * @param array $options
 */
function response_assert_error(PHPUnit_Framework_TestCase $testCase, \Psr\Http\Message\ResponseInterface $response, array $options)
{
    $result = response_to_json($response);

    $testCase->assertObjectHasAttribute('error', $result);
    $testCase->assertObjectNotHasAttribute('data', $result);

    $error = $result->error;
    $testCase->assertObjectHasAttribute('code', $error);
    $testCase->assertObjectHasAttribute('message', $error);

    if (isset($options['code'])) {
        $testCase->assertSame($options['code'], $error->code);
    }

    if (isset($options['status'])) {
        $testCase->assertSame($options['status'], $response->getStatusCode());
    }
}
