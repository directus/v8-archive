<?php

namespace Directus\Tests\Api\Io;

use Directus\Authentication\Exception\ExpiredTokenException;
use Directus\Authentication\Exception\InvalidTokenException;
use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Authentication\Exception\UserInactiveException;
use Directus\Exception\BadRequestException;
use Directus\Util\ArrayUtils;
use Directus\Util\JWTUtils;
use Directus\Validator\Exception\InvalidRequestException;
use GuzzleHttp\Exception\ClientException;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetToken()
    {
        $path = 'collections';
        $response = request_error_get($path);
        assert_response_error($this, $response);

        $path = 'auth/authenticate';
        $response = request_post($path, [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        assert_response($this, $response, [
            'status' => 200,
            'public' => true
        ]);

        $result = response_to_object($response);
        $data = $result->data;
        $this->assertObjectHasAttribute('token', $data);
        $token = $data->token;
        $this->assertTrue(JWTUtils::isJWT($token));
        $payload = JWTUtils::getPayload($token);
        $this->assertInternalType('int', $payload->exp);
        $this->assertInternalType('int', $payload->id);
        $this->assertInternalType('int', $payload->group);

        // Query String
        $path = 'collections';
        $response = request_get($path, ['access_token' => $token]);

        assert_response($this, $response, [
            'status' => 200,
            'data' => 'array'
        ]);

        // Header Authorization
        $path = 'collections';
        $response = request_get($path, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);

        assert_response($this, $response, [
            'status' => 200,
            'data' => 'array'
        ]);

        // Basic Auth
        $path = 'collections';
        $response = request_get($path, [], ['auth' => [$token, null]]);

        assert_response($this, $response, [
            'status' => 200,
            'data' => 'array'
        ]);
    }

    public function testRefreshToken()
    {
        $path = 'auth/authenticate';
        $response = request_post($path, [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        assert_response($this, $response, [
            'status' => 200,
            'public' => true
        ]);

        $result = response_to_object($response);
        $currentToken = $result->data->token;
        $currentPayload = JWTUtils::getPayload($currentToken);

        $path = 'auth/refresh';
        // wait a moment to refresh
        sleep(2);
        $response2 = request_post($path, [
            'token' => $currentToken
        ]);

        $result2 = response_to_object($response2);
        $newToken = $result2->data->token;
        $this->assertNotSame($newToken, $currentToken);
        $newPayload = JWTUtils::getPayload($newToken);

        $this->assertTrue($newPayload->exp > $currentPayload->exp);

        // TODO: Can we test the setting ttl?
    }

    public function testInvalidCredentials()
    {
        try {
            $path = 'auth/authenticate';
            $response = request_post($path, [
                'email' => 'user@getdirectus.com',
                'password' => 'password'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 404,
            'data' => 'array',
            'code' => InvalidUserCredentialsException::ERROR_CODE
        ]);
    }

    public function testDisabledUserCredentials()
    {
        try {
            $path = 'auth/authenticate';
            $response = request_post($path, [
                'email' => 'disabled@getdirectus.com',
                'password' => 'password'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 401,
            'data' => 'array',
            'code' => UserInactiveException::ERROR_CODE
        ]);
    }

    public function testValidation()
    {
        $path = 'auth/authenticate';

        try {
            $response = request_post($path);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);

        try {
            $response = request_post($path, [
                'password' => 'password'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);

        try {
            $response = request_post($path, [
                'email' => 'user@getdirectus.com'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);
    }

    public function testInvalidTokenRefresh()
    {
        $path = 'auth/refresh';
        try {
            $response = request_post($path, [
                'token' => 'token'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 401,
            'code' => InvalidTokenException::ERROR_CODE
        ]);

        try {
            // expired
            $response = request_post($path, [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZ3JvdXAiOjEsImV4cCI6LTF9.KYIEPZn_LC6P8YXEycuxJ2icVojswSbZOJN41r3h7lw'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 401,
            'code' => ExpiredTokenException::ERROR_CODE
        ]);

        try {
            // empty payload
            $response = request_post($path, [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W10.pwrFXDuy0W5KvU5BC7ZjwqssUKcYnkOFRKOUNkwhnkE'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 401,
            'code' => InvalidTokenException::ERROR_CODE
        ]);
    }

    public function testDisabledUserToken()
    {
        $path = 'users';
        $response = request_error_get($path, [
            'access_token' => 'disabled_token'
        ]);

        assert_response_error($this, $response, [
            'status' => 401,
            'code' => UserInactiveException::ERROR_CODE
        ]);
    }
}
