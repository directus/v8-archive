<?php

namespace Directus\Tests\Api\Io;

class AuthenticationTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $url = 'ping';
        $result = request_get($url);

        $this->assertSame('pong', $result);
    }

    public function testGetToken()
    {
        $url = 'tables';
        $result = request_get($url);
        $this->assertInternalType('string', $result);
        $data = json_decode($result, true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('public', $data);
        $this->assertTrue($data['public']);

        $url = 'auth/login';

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
