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
}
