<?php

namespace Directus\Tests\Api;

use Directus\Util\JWTUtils;
use Firebase\JWT\JWT;

class JWTTest extends \PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        $token = JWT::encode([], '123', 'HS256');

        $this->assertTrue(JWTUtils::isJWT($token));
        $this->assertFalse(JWTUtils::isJWT('token'));

        $token = implode('.', [base64_encode('null'), 'k', 'en']);
        $this->assertFalse(JWTUtils::isJWT($token));
        $this->assertFalse(JWTUtils::isJWT(123));

        $token = implode('.', [base64_encode('{"typ": "none"}'), 'k', 'en']);
        $this->assertFalse(JWTUtils::isJWT($token));
    }

    public function testExpiration()
    {
        $data = ['exp' => 0];
        $token = JWTUtils::encode($data, 123);
        $this->assertTrue(JWTUtils::hasExpired($token));

        $data = ['exp' => time() * 2];
        $token = JWTUtils::encode($data, 123);
        $this->assertFalse(JWTUtils::hasExpired($token));

        $data = ['id' => 1];
        $token = JWTUtils::encode($data, 123);
        $this->assertNull(JWTUtils::hasExpired($token));
    }
}
