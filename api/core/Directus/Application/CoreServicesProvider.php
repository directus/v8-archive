<?php

namespace Directus\Application;

use Cache\Adapter\Apc\ApcCachePool;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Adapter\Common\PhpCachePool;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Adapter\Memcache\MemcacheCachePool;
use Cache\Adapter\Memcached\MemcachedCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Adapter\Redis\RedisCachePool;
use Cache\Adapter\Void\VoidCachePool;
use Directus\Application\ErrorHandlers\ErrorHandler;
use Directus\Authentication\Provider;
use Directus\Authentication\User\Provider\UserTableGatewayProvider;
use Directus\Cache\Response;
use Directus\Database\Connection;
use Directus\Database\Object\Column;
use Directus\Database\Object\Table;
use Directus\Database\SchemaManager;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableSchema;
use Directus\Hash\HashManager;
use Directus\Hook\Emitter;
use Directus\Hook\Payload;
use Directus\Permissions\Acl;
use Directus\Services\AuthService;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use League\Flysystem\Adapter\Local;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CoreServicesProvider
{
    public function register($container)
    {
        $container['database']          = $this->getDatabase();
        $container['logger']            = $this->getLogger();
        $container['hook_emitter']      = $this->getEmitter();
        $container['auth']              = $this->getAuth();
        $container['acl']               = $this->getAcl();
        $container['errorHandler']      = $this->getErrorHandler();
        $container['phpErrorHandler']   = $this->getErrorHandler();
        $container['schema_adapter']    = $this->getSchemaAdapter();
        $container['schema_manager']    = $this->getSchemaManager();

        // Move this separately to avoid clogging one class
        $container['cache']             = $this->getCache();
        $container['response_cache']    = $this->getResponseCache();

        $container['services']          = $this->getServices($container);
    }

    /**
     * @return \Closure
     */
    protected function getLogger()
    {
        /**
         * @param Container $container
         * @return Logger
         */
        $logger = function ($container) {
            $logger = new Logger('app');
            $formatter = new LineFormatter();
            $formatter->allowInlineLineBreaks();
            $formatter->includeStacktraces();

            $handler = new StreamHandler(
                $container->get('path')->get('log') . '/debug.' . date('Y-m') . '.log',
                Logger::DEBUG,
                false
            );

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            $handler = new StreamHandler(
                $container->get('path')->get('log') . '/error.' . date('Y-m') . '.log',
                Logger::CRITICAL,
                false
            );

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            return $logger;
        };

        return $logger;
    }

    /**
     * @return \Closure
     */
    protected function getErrorHandler()
    {
        /**
         * @param \Directus\Container\Container $container
         *
         * @return ErrorHandler
         */
        $errorHandler = function ($container) {
            $hookEmitter = $container['hook_emitter'];
            return new ErrorHandler($hookEmitter);
        };

        return $errorHandler;
    }

    /**
     * @return \Closure
     */
    protected function getEmitter()
    {
        return function () {
            $emitter = new Emitter();

            // TODO: Move this separately, this is temporary while we move things around
            $emitter->addFilter('load.relational.onetomany', function (Payload $payload) {
                $rows = $payload->getData();
                /** @var Column $column */
                $column = $payload->attribute('column');

                if ($column->getUI() !== 'translation') {
                    return $payload;
                }

                $options = $column->getOptions();
                $code = ArrayUtils::get($options, 'languages_code_column', 'id');
                $languagesTable = ArrayUtils::get($options, 'languages_table');
                $languageIdColumn = ArrayUtils::get($options, 'left_column_name');

                if (!$languagesTable) {
                    throw new \Exception('Translations language table not defined for ' . $languageIdColumn);
                }

                $tableSchema = TableSchema::getTableSchema($languagesTable);
                $primaryKeyColumn = 'id';
                foreach($tableSchema->getColumns() as $column) {
                    if ($column->isPrimary()) {
                        $primaryKeyColumn = $column->getName();
                        break;
                    }
                }

                $newData = [];
                foreach($rows as $row) {
                    $index = $row[$languageIdColumn];
                    if (is_array($row[$languageIdColumn])) {
                        $index = $row[$languageIdColumn][$code];
                        $row[$languageIdColumn] = $row[$languageIdColumn][$primaryKeyColumn];
                    }

                    $newData[$index] = $row;
                }

                $payload->replace($newData);

                return $payload;
            }, $emitter::P_HIGH);

            return $emitter;
        };
    }

    /**
     * @return \Closure
     */
    protected function getDatabase()
    {
        return function (Container $container) {
            $config = $container->get('config');
            $dbConfig = $config->get('database');

            // TODO: enforce/check required params

            $charset = ArrayUtils::get($dbConfig, 'charset', 'utf8mb4');

            $dbConfig = [
                'driver' => 'Pdo_' . $dbConfig['type'],
                'host' => $dbConfig['host'],
                'port' => $dbConfig['port'],
                'database' => $dbConfig['name'],
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'charset' => $charset,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                \PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES "%s"', $charset)
            ];

            $db = new Connection($dbConfig);

            $db->connect();

            return $db;
        };
    }

    /**
     * @return \Closure
     */
    protected function getAuth()
    {
        return function (Container $container) {
            return new Provider(
                new UserTableGatewayProvider(
                    new DirectusUsersTableGateway($container->get('database'))
                ),
                $container->get('config')->get('auth.secret_key')
            );
        };
    }

    /**
     * @return \Closure
     */
    protected function getAcl()
    {
        return function (Container $container) {
            $acl = new Acl();
            /** @var Provider $auth */
            $auth = $container->get('auth');
            $dbConnection = $container->get('database');

            /** @var Table[] $tables */
            $tables = TableSchema::getTablesSchema([
                'include_columns' => true
            ], true);

            $magicOwnerColumnsByTable = [];
            foreach ($tables as $table) {
                $magicOwnerColumnsByTable[$table->getName()] = $table->getUserCreateColumn();
            }

            // TODO: Move this to a method
            $acl::$cms_owner_columns_by_table = array_merge($magicOwnerColumnsByTable, $acl::$cms_owner_columns_by_table);

            if ($auth->check()) {
                $privilegesTable = new DirectusPrivilegesTableGateway($dbConnection, $acl);
                $acl->setGroupPrivileges($privilegesTable->getGroupPrivileges($auth->getUserAttributes('group')));
            }

            return $acl;
        };
    }

    /**
     * @return \Closure
     */
    protected function getCache()
    {
        return function (Container $container) {
            $config = $container->get('config');
            $poolConfig = $config->get('cache.pool');

            if (!$poolConfig || (!is_object($poolConfig) && empty($poolConfig['adapter']))) {
                $poolConfig = ['adapter' => 'void'];
            }

            if (is_object($poolConfig) && $poolConfig instanceof PhpCachePool) {
                $pool = $poolConfig;
            } else {
                if (!in_array($poolConfig['adapter'], ['apc', 'apcu', 'array', 'filesystem', 'memcached', 'redis', 'void'])) {
                    throw new \Exception("Valid cache adapters are 'apc', 'apcu', 'filesystem', 'memcached', 'redis'");
                }

                $pool = new VoidCachePool();

                $adapter = $poolConfig['adapter'];

                if ($adapter == 'apc') {
                    $pool = new ApcCachePool();
                }

                if ($adapter == 'apcu') {
                    $pool = new ApcuCachePool();
                }

                if ($adapter == 'array') {
                    $pool = new ArrayCachePool();
                }

                if ($adapter == 'filesystem') {
                    if (empty($poolConfig['path'])) {
                        throw new \Exception("'cache.pool.path' parameter is required for 'filesystem' adapter");
                    }

                    $filesystemAdapter = new Local(__DIR__ . '/../../' . $poolConfig['path']);
                    $filesystem = new \League\Flysystem\Filesystem($filesystemAdapter);

                    $pool = new FilesystemCachePool($filesystem);
                }

                if ($adapter == 'memcached') {
                    $host = (isset($poolConfig['host'])) ? $poolConfig['host'] : 'localhost';
                    $port = (isset($poolConfig['port'])) ? $poolConfig['port'] : 11211;

                    $client = new \Memcached();
                    $client->addServer($host, $port);
                    $pool = new MemcachedCachePool($client);
                }

                if ($adapter == 'redis') {
                    $host = (isset($poolConfig['host'])) ? $poolConfig['host'] : 'localhost';
                    $port = (isset($poolConfig['port'])) ? $poolConfig['port'] : 6379;

                    $client = new \Redis();
                    $client->connect($host, $port);
                    $pool = new RedisCachePool($client);
                }
            }

            return $pool;
        };
    }

    /**
     * @return \Closure
     */
    protected function getSchemaAdapter()
    {
        return function (Container $container) {
            $adapter = $container->get('database');
            $databaseName = $adapter->getPlatform()->getName();

            switch ($databaseName) {
                case 'MySQL':
                    return new \Directus\Database\Schemas\Sources\MySQLSchema($adapter);
                // case 'SQLServer':
                //    return new SQLServerSchema($adapter);
                // case 'SQLite':
                //     return new \Directus\Database\Schemas\Sources\SQLiteSchema($adapter);
                // case 'PostgreSQL':
                //     return new PostgresSchema($adapter);
            }

            throw new \Exception('Unknown/Unsupported database: ' . $databaseName);
        };
    }

    /**
     * @return \Closure
     */
    protected function getSchemaManager()
    {
        return function (Container $container) {
            return new SchemaManager(
                $container->get('schema_adapter')
            );
        };
    }

    /**
     * @return \Closure
     */
    protected function getResponseCache()
    {
        return function (Container $container) {
            return new Response($container->get('cache'), $container->get('config')->get('cache.response_ttl'));
        };
    }

    /**
     * @return \Closure
     */
    protected function getHashManager()
    {
        return function (Container $container) {
            $hashManager = new HashManager();

            $path = implode(DIRECTORY_SEPARATOR, [
                BASE_PATH,
                'customs',
                'hashers',
                '*.php'
            ]);

            $customHashersFiles = glob($path);
            $hashers = [];

            if ($customHashersFiles) {
                foreach ($customHashersFiles as $filename) {
                    $name = basename($filename, '.php');
                    // filename starting with underscore are skipped
                    if (StringUtils::startsWith($name, '_')) {
                        continue;
                    }

                    $hashers[] = '\\Directus\\Customs\\Hasher\\' . $name;
                }
            }

            foreach ($hashers as $hasher) {
                $hashManager->register(new $hasher());
            }

            return $hashManager;
        };
    }

    /**
     * Register all services
     *
     * @param Container $container
     *
     * @return \Closure
     */
    protected function getServices(Container $mainContainer)
    {
        // A services container of all Directus services classes
        return function () use ($mainContainer) {
            $container = new Container();

            // =============================================================================
            // Register all services
            // -----------------------------------------------------------------------------
            // TODO: Set a place to load all the services
            // =============================================================================
            $container['auth'] = $this->getAuthService($mainContainer);

            return $container;
        };
    }

    /**
     * @param Container $container Application container
     *
     * @return \Closure
     */
    protected function getAuthService(Container $container)
    {
        return function () use ($container) {
            return new AuthService($container);
        };
    }
}

