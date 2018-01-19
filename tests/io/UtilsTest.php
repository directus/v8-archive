<?php

namespace Directus\Tests\Api\Io;

use Directus\Hash\Exception\HasherNotFoundException;
use Directus\Hash\Exception\MissingValueException;
use Directus\Validator\Exception\InvalidRequestException;

class UtilsTest extends \PHPUnit_Framework_TestCase
{
    protected $availableHashers = [
        'bcrypt',
        'core',
        'md5',
        'sha1',
        'sha224',
        'sha256',
        'sha384',
        'sha512'
    ];

    public function testHash()
    {
        $data = [
            'string' => 'secret'
        ];

        $this->tryHashWith($data);

        foreach ($this->availableHashers as $hasher) {
            $data['hasher'] = $hasher;
            $this->tryHashWith($data);
        }

        $path = 'utils/hash';
        $data['hasher'] = 'none';
        $response = request_error_post($path, $data, ['query' => [
            'access_token' => 'token'
        ]]);

        assert_response_error($this, $response, [
            'code' => HasherNotFoundException::ERROR_CODE,
            'status' => 400
        ]);

        // Empty string passed
        $response = request_error_post($path, ['string' => ''], ['query' => [
            'access_token' => 'token'
        ]]);

        assert_response_error($this, $response, [
            'code' => InvalidRequestException::ERROR_CODE,
            'status' => 400
        ]);

        // Not string passed
        $response = request_error_post($path, [], ['query' => [
            'access_token' => 'token'
        ]]);

        assert_response_error($this, $response, [
            'code' => InvalidRequestException::ERROR_CODE,
            'status' => 400
        ]);
    }

    public function testRandomString()
    {
        $path = 'utils/random_string';
        $queryParams = ['access_token' => 'token'];

        // default length
        $data = [];
        $response = request_post($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        $result = response_to_object($response);
        $data = $result->data;
        $this->assertTrue(strlen($data->random) === 32);

        // specifying the length
        $data = ['length' => 16];
        $response = request_post($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        $result = response_to_object($response);
        $data = $result->data;
        $this->assertTrue(strlen($data->random) === 16);

        // Length not numeric passed
        $response = request_error_post($path, ['length' => 'a'], ['query' => $queryParams]);
        assert_response_error($this, $response, [
            'code' => InvalidRequestException::ERROR_CODE,
            'status' => 400
        ]);
    }

    protected function tryHashWith(array $data)
    {
        $path = 'utils/hash';
        $queryParams = ['access_token' => 'token'];

        $response = request_post($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);

        $result = response_to_object($response);

        $data = $result->data;
        $this->assertInternalType('string', $data->hash);
    }
}
