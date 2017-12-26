<?php

namespace Directus\Tests\Api\Io;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $url = 'http://localhost/api/ping';
        $result = request_get($url);

        $this->assertSame('pong', $result);
    }

    public function testGetToken()
    {
        $url = 'http://localhost/api/tables';
        $result = request_get($url);
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('public', $data);
        $this->assertTrue($data['public']);

        $url = 'http://localhost/api/auth/request_token';

        $result = request_post($url, [], [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);

        $this->assertArrayHasKey('data', $data);
        $this->assertArrayNotHasKey('public', $data);

        $data = $data['data'];
        $this->assertArrayHasKey('token', $data);
    }
}
