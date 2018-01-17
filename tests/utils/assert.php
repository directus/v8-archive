<?php

use Psr\Http\Message\ResponseInterface;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function response_assert(TestCase $testCase, ResponseInterface $response, array $options = [])
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

    // =============================================================================
    // Assert HTTP STATUS CODE
    // =============================================================================
    $statusCode = isset($options['status']) ? $options['status'] : 200;
    $testCase->assertSame($statusCode, $response->getStatusCode());

    if (isset($options['fields'])) {
        $data = $result->data;
        $fields = $options['fields'];

        if ($dataType === 'object') {
            $data = [$data];
        }

        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                $testCase->assertTrue(in_array($key, $fields));
            }
        }
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 */
function response_assert_empty(TestCase $testCase, ResponseInterface $response)
{
    $result = response_to_object($response);

    $testCase->assertEmpty((array)$result);
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function response_assert_meta(TestCase $testCase, ResponseInterface $response, array $options = [])
{
    $result = response_to_object($response);
    $testCase->assertObjectHasAttribute('meta', $result);
    $testCase->assertObjectNotHasAttribute('error', $result);
    $meta = $result->meta;
    $validOptions = ['table', 'type', 'result_count'];

    if (!empty($options)) {
        foreach ($validOptions as $option) {
            if (isset($options[$option])) {
                $testCase->assertObjectHasAttribute($option, $meta);
                $testCase->assertSame($options[$option], $meta->{$option});
            }
        }
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $expectedData
 */
function response_assert_data_contains(TestCase $testCase, ResponseInterface $response, array $expectedData)
{
    $result = response_to_object($response);
    $data = (array)$result->data;

    foreach ($expectedData as $key => $value) {
        $testCase->assertArrayHasKey($key, $data);
        $testCase->assertSame($data[$key], $value);
    }
}

/**
 * @param TestCase $testCase
 * @param ResponseInterface $response
 * @param array $options
 */
function response_assert_error(TestCase $testCase, ResponseInterface $response, array $options)
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
