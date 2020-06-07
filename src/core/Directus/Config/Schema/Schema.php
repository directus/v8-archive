<?php

namespace Directus\Config\Schema;

use Directus\Application\Application;
use Directus\Config\Context;
use Exception;

/**
 * Config schema.
 */
class Schema
{
    public static function readCustomConfig($path) {
        if (file_exists($path . 'CustomSchema.php')) {
            $classes = get_declared_classes();
            require_once $path . 'CustomSchema.php';
            $customClassName = array_diff($classes, get_declared_classes());
            if (count($customClassName) == 0) {
                return;
            }

            // check the class for implements, no need to create the class
            $interfaces = class_implements($customClassName[0]);
            if (isset($interfaces[\CustomSchemaDefine::class ]) &&
                in_array(CustomSchemaDefineTrait::class, class_uses($customClassName))) {
                try {
                    /** @var CustomSchemaDefineTrait $instance */
                    $instance = new $customClassName;
                    $instance->addToSchema();
                } catch (\Exception $exception) {
                    $app = Application::getInstance();
                    $app->getContainer()->get('logger')->error($exception->getMessage());
                }
            }
        }
    }
    static private $customNodes;
    /**
     * Gets the configuration schema.
     *
     * @return Node
     */
    public static function get()
    {
        $isEnv = Context::is_env();
        if ($isEnv) {
            $loggerPath = 'php://stdout';
        } else {
            $loggerPath = realpath(__DIR__.'/../../../../../logs');
        }

        $group = new Group('directus', [
            new Value('env', Types::STRING, 'production'),
            new Group('logger', [
                new Value('path', Types::STRING, $loggerPath),
            ]),
            new Group('database', [
                new Value('type', Types::STRING, 'mysql'),
                new Value('host', Types::STRING, 'localhost'),
                new Value('port', Types::INTEGER, 3306),
                new Value('name', Types::STRING, 'directus'),
                new Value('username', Types::STRING, 'root'),
                new Value('password', Types::STRING, 'root'),
                new Value('engine', Types::STRING, 'InnoDB'),
                new Value('charset', Types::STRING, 'utf8mb4'),
                new Value('socket', Types::STRING, ''),
                new Value('driver_options?', Types::ARRAY, []),
            ]),
            new Group('cache', [
                new Value('enabled', Types::BOOLEAN, false),
                new Value('response_ttl', Types::INTEGER, 3600),
                new Group('pool?', [
                    new Value('adapter', Types::STRING, 'filesystem'),
                    new Value('path', Types::STRING, '../cache/'),
                    new Value('host', Types::STRING, 'localhost'),
                    new Value('auth', Types::STRING, null),
                    new Value('port', Types::INTEGER, 6379),
                ]),
            ]),
            new Group('storage', [
                new Value('adapter', Types::STRING, 'local'),
                new Value('root', Types::STRING, 'public/uploads/_/originals'),
                new Value('root_url', Types::STRING, '/uploads/_/originals'),
                new Value('thumb_root', Types::STRING, 'public/uploads/_/generated'),
                new Value('proxy_downloads?', Types::BOOLEAN, false),

                // S3
                new Value('key?', Types::STRING, 's3-key'),
                new Value('secret?', Types::STRING, 's3-secret'),
                new Value('region?', Types::STRING, 's3-region'),
                new Value('version?', Types::STRING, 's3-version'),
                new Value('bucket?', Types::STRING, 's3-bucket'),
                new Value('endpoint?', Types::STRING, 's3-endpoint'),
                new Group('options', [
                    new Value('ACL', Types::STRING, 'public-read'),
                    new Value('Cache-Control', Types::STRING, 'max-age=604800'),
                ]),

                // OSS
                new Value('OSS_ACCESS_ID?', Types::STRING, 'oss-access-id'),
                new Value('OSS_ACCESS_KEY?', Types::STRING, 'oss-access-secret'),
                new Value('OSS_BUCKET?', Types::STRING, 'oss-bucket'),
                new Value('OSS_ENDPOINT?', Types::STRING, 'oss-endpoint'),

                // TODO: Missing keys?
            ]),
            new Group('mail', [
                new Group('default', [
                    new Value('transport', Types::STRING, 'sendmail'),
                    new Value('from', Types::STRING, 'admin@example.com'),
                    new Value('host?', Types::STRING, ''),
                    new Value('port?', Types::STRING, ''),
                    new Value('username?', Types::STRING, ''),
                    new Value('password?', Types::STRING, ''),
                    new Value('encryption?', Types::STRING, ''),
                ]),
            ]),
            new Group('cors', [
                new Value('enabled', Types::BOOLEAN, true),
                new Value('origin', Types::ARRAY, ['*']),
                new Value('methods', Types::ARRAY, [
                    'GET',
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE',
                    'HEAD',
                ]),
                new Value('headers', Types::ARRAY, []),
                new Value('exposed_headers', Types::ARRAY, []),
                new Value('max_age', Types::INTEGER, null),
                new Value('credentials', Types::BOOLEAN, false),
            ]),
            new Group('rate_limit', [
                new Value('enabled', Types::BOOLEAN, false),
                new Value('limit', Types::INTEGER, 100),
                new Value('interval', Types::INTEGER, 60),
                new Value('adapter', Types::STRING, 'redis'),
                new Value('host', Types::STRING, '127.0.0.1'),
                new Value('port', Types::INTEGER, 6379),
                new Value('timeout', Types::INTEGER, 10),
            ]),
            new Group('hooks', [
                new Value('actions', Types::ARRAY, []),
                new Value('filters', Types::ARRAY, []),
            ]),
            new Value('tableBlacklist', Types::ARRAY, []),
            new Group('auth', [
                new Value('secret_key', Types::STRING, '<type-a-secret-authentication-key-string>'),
                new Value('public_key', Types::STRING, '<type-a-public-authentication-key-string>'),
                new Group('social_providers', [
                    new Group('okta?', [
                        new Value('client_id', Types::STRING, ''),
                        new Value('client_secret', Types::STRING, ''),
                        new Value('base_url', Types::STRING, 'https://dev-000000.oktapreview.com/oauth2/default'),
                    ]),
                    new Group('github?', [
                        new Value('client_id', Types::STRING, ''),
                        new Value('client_secret', Types::STRING, ''),
                    ]),
                    new Group('facebook?', [
                        new Value('client_id', Types::STRING, ''),
                        new Value('client_secret', Types::STRING, ''),
                        new Value('graph_api_version', Types::STRING, 'v2.8'),
                    ]),
                    new Group('google?', [
                        new Value('client_id', Types::STRING, ''),
                        new Value('client_secret', Types::STRING, ''),
                        new Value('hosted_domain', Types::STRING, '*'),
                        new Value('use_oidc_mode', Types::BOOLEAN, true),
                    ]),
                    new Group('twitter?', [
                        new Value('identifier', Types::STRING, ''),
                        new Value('secret', Types::STRING, ''),
                    ]),
                ]),
            ]),
            new Value('ext?', Types::ARRAY, []),
        ]);

        if (Context::has_custom_context()) {
            foreach (self::$customNodes as $path => $nodes) {
                foreach ($nodes as $node) {
                    $group = self::saveCustomNode($group, explode('_', $path), $node);
                }
            }
        }
        return $group;
    }

    private static function normalizeNodeName($element)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $element);
    }

    /**
     * @param Base|Node|Node[] $group
     * @param array $keys
     * @param Value $newChild
     * @return Base
     */
    private static function saveCustomNode($group, $keys, $newChild)
    {
        $key = strtolower(self::normalizeNodeName(array_shift($keys)));
        if (count($keys) == 0) {
            return $group->addChild($newChild);
        }
        foreach($group->children() as $child) {
            if ($child->key() === $key) {
                if (count($keys) >= 1) {
                    return $group->addChild(self::saveCustomNode($child, $keys, $newChild));
                }
            }
        }
        if (count($keys) > 0) {
            $newGroup = new Group($key, []);
            $newGroup = self::saveCustomNode($newGroup, $keys, $newChild);
            return $group->addChild($newGroup);
        }
        return $group;
    }

    /**
     * accepts a complete path to a custom config place
     * @param string $path the path to be added to the config eg AUTH_SOCIAL-PROVIDER_KEYCLOAK
     * @param Value $value
     * @throws Exception
     */
    public static function registerCustomNode($path, $value) {
        if (!($value instanceof Value)) {
            throw new Exception('second parameter must be of type ' . Value::class);
        }

        self::$customNodes[$path][] = $value;
    }
}
