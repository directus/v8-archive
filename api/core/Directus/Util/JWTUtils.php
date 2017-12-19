<?php

namespace Directus\Util;

use Firebase\JWT\JWT;

class JWTUtils
{
    /**
     * Checks whether a token is a JWT token
     *
     * @param $token
     *
     * @return bool
     */
    public static function isJWT($token)
    {
        if (!is_string($token)) {
            return false;
        }

        $parts = explode('.', $token);
        if (count($parts) != 3) {
            return false;
        }

        list($headb64, $bodyb64, $cryptob64) = $parts;
        if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
            return false;
        }

        return $header->typ === 'JWT';
    }
}
