<?php

// For more complex, volume-mounted configuration files
$path = join(DIRECTORY_SEPARATOR, [__DIR__, "docker", "api.php"]);
if (file_exists($path)) {
    return require $path;
}

/**
 * Gets a string value
 */
if (!function_exists("getenvs")) {
    function getenvs($name, $default) {
        $value = getenv($name);
        if ($value === false) {
            return $default;
        } else {
            return $value;
        }
    }
}

/**
 * Gets an integer value
 */
if (!function_exists("getenvi")) {
    function getenvi($name, $default) {
        $value = getenv($name);
        if ($value === false) {
            return $default;
        } else {
            $value = intval(trim($value));
            return $value;
        }
    }
}

/**
 * Gets a boolean value
 */
if (!function_exists("getenvb")) {
    function getenvb($name, $default) {
        $value = getenv($name);
        if ($value === false) {
            return $default;
        } else {
            $value = trim(strtolower($value));
            return $value === "true" || $value ===  "1" || $value === "on" || $value === "yes";
        }
    }
}

/**
 * Gets all environment variable keys that match key prefix
 */
if (!function_exists("getenvk")) {
    function getenvk($prefix) {
        $keys = [];
        foreach ($_ENV as $key => $value) {
            if (substr($key, 0, strlen($prefix)) === $prefix) {
                $keys[] = $key;
            }
        }
        return $keys;
    }
}

/**
 * Reads the configuration
 */
if (!function_exists("api_config_read")) {
    function api_config_read() {
        $database = [
            'type' => getenvs('DATABASE_TYPE', 'mysql'),
            'port' => getenvi('DATABASE_PORT', 3306),
            'name' => getenvs('DATABASE_NAME', 'directus'),
            'username' => getenvs('DATABASE_USERNAME', 'root'),
            'password' => getenvs('DATABASE_PASSWORD', 'root'),
            'engine' => getenvs('DATABASE_ENGINE', 'InnoDB'),
            'charset' => getenvs('DATABASE_CHARSET', 'utf8mb4')
        ];

        $database_socket = getenvs('DATABASE_SOCKET', false);
        if ($database_socket !== false) {
            $database['socket'] = $database_socket;
        } else {
            $database['host'] = getenvs('DATABASE_HOST', 'localhost');
        }

        $config = [
            'app' => [
                'env' => getenvs('APP_ENV', 'production'),
                'timezone' => getenvs('APP_TIMEZONE', 'America/New_York'),
            ],

            'settings' => [
                'logger' => [
                    'path' => getenvs('SETTINGS_LOGGER_PATH', __DIR__ . '/logs/app.log'),
                ],
            ],

            'database' => $database,

            'cache' => [
                'enabled' => getenvb('CACHE_ENABLED', false),
                'response_ttl' => getenvi('CACHE_RESPONSE_TTL', 3600), // seconds
            ],

            'storage' => [
                'adapter' => getenvs('STORAGE_ADAPTER', 'local'),
                'root' => getenvs('STORAGE_ROOT', 'public/uploads/_/originals'),
                'root_url' => getenvs('STORAGE_ROOT_URL', '/uploads/_/originals'),
                'thumb_root' => getenvs('STORAGE_THUMB_URL', 'public/uploads/_/thumbnails'),
                'key' => getenvs('STORAGE_KEY', 's3-key'),
                'secret' => getenvs('STORAGE_SECRET', 's3-secret'),
                'region' => getenvs('STORAGE_REGION', 'us-east-1'),
                'version' => getenvs('STORAGE_VERSION', 'latest'),
                'bucket' => getenvs('STORAGE_BUCKET', 'directus'),
            ],

            'mail' => [
                'default' => [
                    'transport' => getenvs('MAIL_DEFAULT_TRANSPORT', 'sendmail'),
                    'from' => getenvs('MAIL_DEFAULT_FROM', 'admin@example.com')
                ],
            ],

            'cors' => [
                'enabled' => getenvb('CORS_ENABLED', true),
                'origin' => ['*'],
                'methods' => [
                    'GET',
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE',
                    'HEAD',
                ],
                'headers' => [],
                'exposed_headers' => [],
                'max_age' => getenvi('CORS_MAX_AGE', null), // in seconds
                'credentials' => getenvs('CORS_CREDENTIALS', false),
            ],

            'rate_limit' => [
                'enabled' => getenvb('RATE_LIMIT_ENABLED', false),
                'limit' => getenvi('RATE_LIMIT_LIMIT', 100), // number of request
                'interval' => getenvi('RATE_LIMIT_INTERVAL', 60), // seconds
                'adapter' => getenvs('RATE_LIMIT_ADAPTER', 'redis'),
                'host' => getenvs('RATE_LIMIT_HOST', '127.0.0.1'),
                'port' => getenvi('RATE_LIMIT_PORT', 6379),
                'timeout' => getenvi('RATE_LIMIT_TIMEOUT', 10)
            ],

            'hooks' => [],

            'filters' => [],

            'feedback' => [
                'token' => getenvs('FEEDBACK_TOKEN', 'a-kind-of-unique-token'),
                'login' => getenvb('FEEDBACK_LOGIN', true)
            ],

            'tableBlacklist' => [],

            'auth' => [
                'secret_key' => getenvs('AUTH_SECRET_KEY', '<type-a-secret-authentication-key-string>'),
                'public_key' => getenvs('AUTH_PUBLIC_KEY', '<type-a-public-authentication-key-string>'),
                'social_providers' => [
                ]
            ],
        ];

        $endpoint = getenvs('STORAGE_ENDPOINT', false);
        if ($endpoint !== false) {
            $config['storage']['endpoint'] = $endpoint;
        }

        if (sizeof(getenvk('AUTH_OKTA_')) > 0) {
            $config['auth']['social_providers']['okta'] = [
                'client_id' => getenvs('AUTH_OKTA_CLIENT_ID', ''),
                'client_secret' => getenvs('AUTH_OKTA_CLIENT_SECRET', ''),
                'base_url' => getenvs('AUTH_OKTA_BASE_URL', 'https://dev-000000.oktapreview.com/oauth2/default')
            ];
        }

        if (sizeof(getenvk('AUTH_GITHUB_')) > 0) {
            $config['auth']['social_providers']['github'] = [
                'client_id' => getenvs('AUTH_GITHUB_CLIENT_ID', ''),
                'client_secret' => getenvs('AUTH_GITHUB_CLIENT_SECRET', ''),
            ];
        }

        if (sizeof(getenvk('AUTH_FACEBOOK_')) > 0) {
            $config['auth']['social_providers']['facebook'] = [
                'client_id' => getenvs('AUTH_FACEBOOK_CLIENT_ID', ''),
                'client_secret' => getenvs('AUTH_FACEBOOK_CLIENT_SECRET', ''),
                'graph_api_version' => getenvs('AUTH_FACEBOOK_GRAPH_API_VERSION', 'v2.8'),
            ];
        }

        if (sizeof(getenvk('AUTH_GOOGLE_')) > 0) {
            $config['auth']['social_providers']['google'] = [
                'client_id' => getenvs('AUTH_GOOGLE_CLIENT_ID', ''),
                'client_secret' => getenvs('AUTH_GOOGLE_CLIENT_SECRET', ''),
                'hosted_domain' => getenvs('AUTH_GOOGLE_HOSTED_DOMAIN', '*'),
            ];
        }

        if (sizeof(getenvk('AUTH_TWITTER_')) > 0) {
            $config['auth']['social_providers']['twitter'] = [
                'identifier' => getenvs('AUTH_TWITTER_IDENTIFIER', ''),
                'secret' => getenvs('AUTH_TWITTER_SECRET', ''),
            ];
        }

        $pool = getenvk('CACHE_POOL_');
        if (sizeof($pool) > 0) {
            $value = [];
            foreach ($pool as $key) {
                $name = strtolower(substr($key, strlen('CACHE_POOL_')));
                $value[$name] = getenvs($key, '');
            }
            $config['cache']['pool'] = $value;
        }

        return $config;
    }
}

return api_config_read();
