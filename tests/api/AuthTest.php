<?php
use Directus\Api\Routes\Auth as Auth;
use GuzzleHttp\Client;
class AuthTest extends PHPUnit_Framework_TestCase
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost/directus-api/public/_/',
            'exceptions' => false
        ]);
    }

    public function tearDown() {
        $this->http = null;
    }
    
    public function testAuthentication()
    {

        $data = [
            'form_params' => [
                'email' => "admin@example.com",
                'password' => "password"
            ]
        ];
        
        $response = $this->http->request('POST', 'auth/authenticate', $data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('token', $data['data']);
        $this->assertTrue(!empty($data['data']['token']));
        
    }
    
    public function testForgotPassword()
    {

        $data = [
            'form_params' => [
                'email' => "admin@example.com"
            ]
        ];
        
        $response = $this->http->request('POST', 'auth/password/request', $data);
        
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertTrue($data['public']);
        
    }
}