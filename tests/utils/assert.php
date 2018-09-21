<?php

use Psr\Http\Message\ResponseInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function assert_response(TestCase $testCase, ResponseInterface $response, array $options = [])
{
    $result = response_to_object($response);

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

    if (isset($options['empty']) && $options['empty'] == true) {
        $testCase->assertEmpty($result->data);
    }

    // =============================================================================
    // Assert HTTP STATUS CODE
    // =============================================================================
    $statusCode = isset($options['status']) ? $options['status'] : 200;
    $testCase->assertSame($statusCode, $response->getStatusCode());

    if (isset($options['fields'])) {
        $hasFields = isset($options['has_fields']) ? (bool)$options['has_fields'] : false;
        $data = (array)$result->data;
        $fields = $options['fields'];

        if ($dataType === 'object') {
            $data = [$data];
        }

        if ($hasFields === true) {
            foreach ($fields as $field) {
                foreach ($data as $item) {
                    $testCase->assertTrue(in_array($field, array_keys($item)));
                }
            }
        } else {
            foreach ($data as $item) {
                foreach ($item as $key => $value) {
                    $testCase->assertTrue(in_array($key, $fields));
                }
            }
        }
    }
}

/**
 * Tests whether the response content match a given content
 *
 * @param PHPUnit_Framework_TestCase $testCase
 * @param ResponseInterface $response
 * @param string $content
 * @param array $options
 */
function assert_response_contents(TestCase $testCase, ResponseInterface $response, $content, array $options = [])
{
    $bodyContent = response_get_body_contents($response);
    $testCase->assertSame($content, $bodyContent);

    if (isset($options['status'])) {
        $testCase->assertSame($options['status'], $response->getStatusCode());
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 */
function assert_response_empty(TestCase $testCase, ResponseInterface $response)
{
    $result = response_to_object($response);

    $testCase->assertEmpty((array)$result);
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function assert_response_meta(TestCase $testCase, ResponseInterface $response, array $options = [])
{
    $result = response_to_object($response);
    $testCase->assertObjectHasAttribute('meta', $result);
    $testCase->assertObjectNotHasAttribute('error', $result);
    $meta = $result->meta;

    // TODO: Check for 204 status code
    foreach ($options as $key => $option) {
        $testCase->assertObjectHasAttribute($key, $meta);
        $testCase->assertSame($option, $meta->{$key});
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $fields
 */
function assert_response_meta_fields(TestCase $testCase, ResponseInterface $response, array $fields = [])
{
    $result = response_to_object($response);
    $testCase->assertObjectHasAttribute('meta', $result);
    $testCase->assertObjectNotHasAttribute('error', $result);
    $meta = $result->meta;

    // TODO: Check for 204 status code
    foreach ($fields as $field) {
        $testCase->assertObjectHasAttribute($field, $meta);
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $expectedData
 * @param bool $strict
 */
function assert_response_data_contains(TestCase $testCase, ResponseInterface $response, array $expectedData, $strict = true)
{
    $result = response_to_object($response);
    $data = (array)$result->data;

    foreach ($expectedData as $key => $value) {
        $testCase->assertArrayHasKey($key, $data);
        if ($strict) {
            $testCase->assertSame($value, $data[$key]);
        } else {
            $testCase->assertEquals($value, $data[$key]);
        }
    }
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param ResponseInterface $response
 * @param array $expectedKeys
 */
function assert_response_data_fields(TestCase $testCase, ResponseInterface $response, array $expectedKeys)
{
    $result = response_to_object($response);

    assert_data_fields($testCase, $result->data, $expectedKeys);
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param array|object $data
 * @param array $expectedKeys
 */
function assert_data_fields(TestCase $testCase, $data, array $expectedKeys)
{
    if (is_object($data)) {
        $data = [(array)$data];
    }

    foreach ($data as $row) {
        $testCase->assertCount(count($expectedKeys), (array)$row, 'Expected fields are exactly on each data row');

        foreach ($row as $field => $value) {
            $testCase->assertTrue(in_array($field, $expectedKeys));
        }
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function assert_response_error(TestCase $testCase, ResponseInterface $response, array $options = [])
{
    $result = response_to_object($response);

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
