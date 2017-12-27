<?php

namespace Directus\Tests\Api\Io;

use Directus\Authentication\Exception\ExpiredTokenException;
use Directus\Authentication\Exception\InvalidTokenException;
use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Util\ArrayUtils;
use Directus\Util\JWTUtils;
use GuzzleHttp\Exception\ClientException;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetToken()
    {
        $path = 'tables';
        $response = request_get($path);

        $this->assertSame(200, $response->getStatusCode());

        $result = $response->getBody()->getContents();

        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('public', $data);
        $this->assertTrue($data['public']);

        $path = 'auth/login';
        $response = request_post($path, [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $result = $response->getBody()->getContents();
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayNotHasKey('public', $data);

        $data = $data['data'];
        $this->assertArrayHasKey('token', $data);
        $this->assertTrue(JWTUtils::isJWT($data['token']));

        $token = $data['token'];
        // Query String
        $path = 'tables';
        $response = request_get($path, ['access_token' => $data['token']]);
        $this->assertSame(200, $response->getStatusCode());

        $result = $response->getBody()->getContents();
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertInternalType('array', $data['data']);
        $this->assertArrayNotHasKey('public', $data);

        // Header Authorization
        $path = 'tables';
        $response = request_get($path, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertSame(200, $response->getStatusCode());

        $result = $response->getBody()->getContents();
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertInternalType('array', $data['data']);
        $this->assertArrayNotHasKey('public', $data);

        // Basic Auth
        $path = 'tables';
        $response = request_get($path, [], ['auth' => [$token, null]]);
        $this->assertSame(200, $response->getStatusCode());
        $result = $response->getBody()->getContents();
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertInternalType('array', $data['data']);
        $this->assertArrayNotHasKey('public', $data);
    }

    public function testRefreshToken()
    {
        $path = 'auth/login';
        $response = request_post($path, [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $currentToken = ArrayUtils::get($result, 'data.token');
        $currentPayload = JWTUtils::getPayload($currentToken);

        $path = 'auth/refresh';
        // wait a moment to refresh
        sleep(2);
        $response2 = request_post($path, [
            'token' => $currentToken
        ]);

        $result2 = json_decode($response2->getBody()->getContents(), true);
        $newToken = ArrayUtils::get($result2, 'data.token');
        $this->assertNotSame($newToken, $currentToken);
        $newPayload = JWTUtils::getPayload($newToken);

        $this->assertTrue($newPayload->exp > $currentPayload->exp);

        // TODO: Can we test the setting ttl?
    }

    public function testInvalidCredentials()
    {
        try {
            $path = 'auth/login';
            $response = request_post($path, [
                'email' => 'user@getdirectus.com',
                'password' => 'password'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(404, $response->getStatusCode());
        $result = $response->getBody()->getContents();
        $data = json_decode($result, true);
        $this->assertInternalType('array', $data);
        $this->assertArrayNotHasKey('data', $data);
        $this->assertArrayHasKey('error', $data);
        $error = $data['error'];
        $this->assertArrayHasKey('code', $error);
        $this->assertArrayHasKey('message', $error);
        $this->assertSame(InvalidUserCredentialsException::ERROR_CODE, $error['code']);
    }

    public function testValidation()
    {
        $path = 'auth/login';

        try {
            $response = request_post($path);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(400, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(0002, $error['code']);

        try {
            $response = request_post($path, [
                'password' => 'password'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(400, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(0002, $error['code']);

        try {
            $response = request_post($path, [
                'email' => 'user@getdirectus.com'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(400, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(0002, $error['code']);
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

        $this->assertSame(401, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(InvalidTokenException::ERROR_CODE, $error['code']);

        try {
            // expired
            $response = request_post($path, [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiZ3JvdXAiOjEsImV4cCI6LTF9.KYIEPZn_LC6P8YXEycuxJ2icVojswSbZOJN41r3h7lw'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(401, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(ExpiredTokenException::ERROR_CODE, $error['code']);

        try {
            // empty payload
            $response = request_post($path, [
                'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W10.pwrFXDuy0W5KvU5BC7ZjwqssUKcYnkOFRKOUNkwhnkE'
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        $this->assertSame(401, $response->getStatusCode());
        $result = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('error', $result);
        $error = $result['error'];
        $this->assertSame(InvalidTokenException::ERROR_CODE, $error['code']);
    }
}
