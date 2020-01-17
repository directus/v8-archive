<?php

namespace Directus\Config;

use Directus\Exception\UnauthorizedException;
use RuntimeException;

/**
 * Config context interface.
 */
class SuperAdminToken
{
    /**
     * Gets the super admin token.
     *
     * @return string
     */
    public static function get()
    {
        if (Context::is_env()) {
            $token = getenv('DIRECTUS_SUPER_ADMIN_TOKEN');
            if (!$token) {
                throw new RuntimeException('Environment variable `DIRECTUS_SUPER_ADMIN_TOKEN` is not set.');
            }

            return $token;
        }

        $basePath = \Directus\get_app_base_path();
        $filePath = $basePath.'/config/__api.json';

        if (!file_exists($filePath)) {
            throw new RuntimeException('Cannot find config/__api.json file.');
        }

        $fileData = json_decode(file_get_contents($filePath), true);

        return $fileData['super_admin_token'];
    }

    /**
     * Validates if the token is valid. Doesn't throw exceptions.
     *
     * @param string $token Token string
     *
     * @return bool
     */
    public static function validate($token)
    {
        return $token === static::get();
    }

    /**
     * Asserts the super admin token.
     *
     * @param string $token Token string
     *
     * @return bool
     */
    public static function assert($token)
    {
        if (!static::validate($token)) {
            throw new UnauthorizedException('Permission denied: Superadmin Only');
        }

        return true;
    }
}
