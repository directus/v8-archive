<?php

namespace Directus;

use Cache\Adapter\Apc\ApcCachePool;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Adapter\Common\PhpCachePool;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Adapter\Memcached\MemcachedCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Adapter\Redis\RedisCachePool;
use Cache\Adapter\Void\VoidCachePool;
use Directus\Application\Application;
use Directus\Application\Container;
use Directus\Authentication\FacebookProvider;
use Directus\Authentication\GitHubProvider;
use Directus\Authentication\GoogleProvider;
use Directus\Authentication\Provider as AuthProvider;
use Directus\Authentication\Provider;
use Directus\Authentication\Social;
use Directus\Authentication\TwitterProvider;
use Directus\Authentication\User\Provider\UserTableGatewayProvider;
use Directus\Cache\Response as ResponseCache;
use Directus\Cache\Response;
use Directus\Config\Config;
use Directus\Database\Connection;
use Directus\Database\Object\Table;
use Directus\Database\SchemaManager;
use Directus\Database\Schemas\Sources\MySQLSchema;
use Directus\Database\Schemas\Sources\SQLiteSchema;
use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Database\TableGateway\DirectusSettingsTableGateway;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableGatewayFactory;
use Directus\Database\TableSchema;
use Directus\Debug\Log\Writer;
use Directus\Embed\EmbedManager;
use Directus\Exception\Exception;
use Directus\Exception\Http\ForbiddenException;
use Directus\Filesystem\Filesystem;
use Directus\Filesystem\FilesystemFactory;
use Directus\Filesystem\Thumbnail;
use Directus\Hash\HashManager;
use Directus\Hook\Emitter;
use Directus\Hook\Payload;
use Directus\Language\LanguageManager;
use Directus\Permissions\Acl;
use Directus\Providers\FilesServiceProvider;
use Directus\Services\AuthService;
use Directus\Session\Session;
use Directus\Session\Storage\NativeSessionStorage;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;
use Directus\Util\StringUtils;
use Directus\View\Twig\DirectusTwigExtension;
use Slim\Extras\Views\Twig;
use League\Flysystem\Adapter\Local;
use Slim\Helper\Set;


/**
 * NOTE: This class depends on the constants defined in config.php
 */
class Bootstrap
{
    public static $singletons = [];

    /**
     * Returns the instance of the specified singleton, instantiating one if it
     * doesn't yet exist.
     *
     * @param  string $key The name of the singleton / singleton factory function
     * @param  mixed $arg An argument to be passed to the singleton factory function
     * @param  bool $newInstance return new instance rather than singleton instance (useful for long running scripts to get a new Db Conn)
     *
     * @return mixed The singleton with the specified name
     */
    public static function get($key, $arg = null, $newInstance = false)
    {
        $key = strtolower($key);
        if (!method_exists(__CLASS__, $key)) {
            throw new \InvalidArgumentException('No such factory function on ' . __CLASS__ . ': ' . $key);
        }
        if ($newInstance) {
            return call_user_func(__CLASS__ . '::' . $key, $arg);
        }
        if (!array_key_exists($key, self::$singletons)) {
            self::$singletons[$key] = call_user_func(__CLASS__ . '::' . $key, $arg);
        }
        return self::$singletons[$key];
    }

    /**
     * Does an extension by the given name exist?
     * @param  string $extensionName
     * @return bool
     */
    public static function extensionExists($extensionName)
    {
        $extensions = self::get('extensions');
        return array_key_exists($extensionName, $extensions);
    }

    /**
     * Get all custom endpoints
     * @return array - list of endpoint files loaded
     * @throws \Exception
     */
    public static function getCustomEndpoints()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $endpointsDirectory = APPLICATION_PATH . '/customs/endpoints';

        if (!file_exists($endpointsDirectory)) {
            return [];
        }

        return find_php_files($endpointsDirectory, true);
    }

    /**
     * SINGLETON FACTORY FUNCTIONS
     */

    /**
     * Make Slim app.
     *
     * @return Application
     */
    private static function app()
    {
        // TODO: Temporary, until we get rid of this Bootstrap object
        return Application::getInstance();
        self::requireConstants(['DIRECTUS_ENV', 'APPLICATION_PATH'], __FUNCTION__);
        $loggerSettings = [
            'path' => APPLICATION_PATH . '/api/logs'
        ];

        $templatesPaths = [APPLICATION_PATH . '/api/views/', APPLICATION_PATH . '/templates/'];
        $app = new Application([
            'templates.path' => $templatesPaths[0],
            'mode' => DIRECTUS_ENV,
            'debug' => false,
            'log.enable' => true,
            'log.writer' => new Writer($loggerSettings),
            'view' => new Twig()
        ]);

        Twig::$twigTemplateDirs = $templatesPaths;

        Twig::$twigExtensions = [
            new DirectusTwigExtension()
        ];

        $app->container->singleton('hookEmitter', function () {
            return Bootstrap::get('hookEmitter');
        });

        $app->container->singleton('session', function () {
            return Bootstrap::get('session');
        });

        $app->container->singleton('socialAuth', function() {
            return Bootstrap::get('socialAuth');
        });

        $config = defined('BASE_PATH') ? Bootstrap::get('config') : new Config();
        $app->container->set('config', $config);

        $app->container->singleton('cache', function() {
            return Bootstrap::get('cache');
        });

        $app->container->singleton('responseCache', function() {
            return Bootstrap::get('responseCache');
        });

        $authConfig = $config->get('auth', []);
        $socialAuth = $app->container->get('socialAuth');

        $socialAuthServices = static::getSocialAuthServices();
        foreach ($socialAuthServices as $name => $class) {
            if (ArrayUtils::has($authConfig, $name)) {
                $config = ArrayUtils::get($authConfig, $name);
                $socialAuth->register(new $class($app, $config));
            }
        }

        // NOTE: Trying to separate the configuration from bootstrap, bit by bit.
        TableSchema::setConfig(static::get('config'));
        $app->register(new FilesServiceProvider());

        $app->container->singleton('filesystem', function() {
            return Bootstrap::get('filesystem');
        });

        $app->container->get('session')->start();

        return $app;
    }

    private static function getSocialAuthServices()
    {
        return [
            'github' => GitHubProvider::class,
            'facebook' => FacebookProvider::class,
            'twitter' => TwitterProvider::class,
            'google' => GoogleProvider::class
        ];
    }

    private static function config()
    {
        $config = [];
        if (defined('APPLICATION_PATH')) {
            $configPath = APPLICATION_PATH . '/api/configuration.php';
            if (file_exists($configPath)) {
                $config = require $configPath;
            }
        }

        return new Config($config);
    }

    private static function status()
    {
        $config = self::get('config');
        $status = $config['statusMapping'];
        return $status;
    }

    private static function mailer()
    {
        $config = self::get('config');
        if (!$config->has('mail')) {
            return null;
        }

        $mailConfig = $config->get('mail');
        switch ($mailConfig['transport']) {
            case 'smtp':
                $transport = \Swift_SmtpTransport::newInstance($mailConfig['host'], $mailConfig['port']);

                if (array_key_exists('username', $mailConfig)) {
                    $transport->setUsername($mailConfig['username']);
                }

                if (array_key_exists('password', $mailConfig)) {
                    $transport->setPassword($mailConfig['password']);
                }

                if (array_key_exists('encryption', $mailConfig)) {
                    $transport->setEncryption($mailConfig['encryption']);
                }
                break;
            case 'sendmail':
                $transport = \Swift_SendmailTransport::newInstance($mailConfig['sendmail']);
                break;
            case 'mail':
            default:
                $transport = \Swift_MailTransport::newInstance();
                break;
        }

        $mailer = \Swift_Mailer::newInstance($transport);

        return $mailer;
    }

    /**
     * Yield Slim logger
     * @return \Slim\Extras\Log\DateTimeFileWriter
     */
    private static function log()
    {
        return self::get('app')->getLog();
    }

    private static function zendDb()
    {
        /** @var Application $app */
        $app = static::get('app');

        return $app->getContainer()->get('database');
    }

    private static function schemaManager()
    {
        $app = static::get('app');

        return $app->getContainer()->get('schema_manager');
    }

    /**
     * Construct Acl provider
     * @return \Directus\Permissions\Acl
     */
    private static function acl()
    {
        $acl = static::get('app')->getContainer()->get('acl');

        return $acl;
    }

    private static function auth()
    {
        return static::get('app')->getContainer()->get('auth');
    }

    private static function filesystem()
    {
        $config = self::get('config');
        return new Filesystem(FilesystemFactory::createAdapter($config['filesystem']));
    }

    /**
     * Scan for extensions.
     * @return  array
     */
    private static function extensions()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $extensions = [];
        $extensionsDirectory = APPLICATION_PATH . '/customs/extensions/';

        if (!file_exists($extensionsDirectory)) {
            return $extensions;
        }

        foreach (new \DirectoryIterator($extensionsDirectory) as $file) {
            if ($file->isDot()) {
                continue;
            }
            $extensionName = $file->getFilename();

            // Ignore all extensions prefixed with an underscore
            if ($extensionName[0] == '_') {
                continue;
            }

            if (is_dir($extensionsDirectory . $extensionName)) {
                $extensions[$extensionName] = "extensions/$extensionName/main";
            }
        }
        return $extensions;
    }

    /**
     * Scan for interfaces.
     * @return  array
     */
    private static function interfaces()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $uiBasePath = APPLICATION_PATH . '/customs';
        $uiDirectory = $uiBasePath . '/interfaces';
        $uis = [];

        if (!file_exists($uiDirectory)) {
            return $uis;
        }

        $filePaths = find_directories($uiDirectory);
        foreach ($filePaths as $path) {
            $path .= '/component.js';
            if (!file_exists($path)) {
                continue;
            }

            $uiPath = trim(substr($path, strlen($uiBasePath)), '/');
            $uis[] = substr($uiPath, 0, -3);
        }

        return $uis;
    }


    /**
     * Scan for listviews.
     * @return  array
     */
    private static function listViews()
    {
        self::requireConstants('APPLICATION_PATH', __FUNCTION__);
        $listViews = [];
        $listViewsDirectory = APPLICATION_PATH . '/customs/listviews/';

        if (!file_exists($listViewsDirectory)) {
            return $listViews;
        }

        foreach (new \DirectoryIterator($listViewsDirectory) as $file) {
            if ($file->isDot()) {
                continue;
            }
            $listViewName = $file->getFilename();
            if (is_dir($listViewsDirectory . $listViewName)) {
                $listViews[] = "listviews/$listViewName/ListView";
            }
        }
        return $listViews;
    }

    /**
     * @return \Directus\Language\LanguageManager
     */
    private static function languagesManager()
    {
        $languages = get_locales_filename();

        return new LanguageManager($languages);
    }

    /**
     * @return \Directus\Embed\EmbedManager
     */
    private static function embedManager()
    {
        $embedManager = new EmbedManager();

        $acl = static::get('acl');
        $adapter = static::get('ZendDb');

        // Fetch files settings
        $SettingsTable = new DirectusSettingsTableGateway($adapter, $acl);
        try {
            $settings = $SettingsTable->fetchCollection('files', [
                'thumbnail_size', 'thumbnail_quality', 'thumbnail_crop_enabled'
            ]);
        } catch (\Exception $e) {
            $settings = [];
            $log = static::get('log');
            $log->warn($e);
        }

        $providers = [
            '\Directus\Embed\Provider\VimeoProvider',
            '\Directus\Embed\Provider\YoutubeProvider'
        ];

        $path = implode(DIRECTORY_SEPARATOR, [
            BASE_PATH,
            'customs',
            'embeds',
            '*.php'
        ]);

        $customProvidersFiles = glob($path);
        if ($customProvidersFiles) {
            foreach ($customProvidersFiles as $filename) {
                $providers[] = '\\Directus\\Embed\\Provider\\' . basename($filename, '.php');
            }
        }

        foreach ($providers as $providerClass) {
            $provider = new $providerClass($settings);
            $embedManager->register($provider);
        }

        return $embedManager;
    }

    private static function hashManager()
    {
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
    }

    /**
     * Get Hook Emitter
     *
     * @return Emitter
     */
    private static function hookEmittersssss()
    {
        $emitter = new Emitter();

        // TODO: Move all this filters to a dedicated file/class/function

        // Cache subscriptions
        $cachePool = Bootstrap::get('cache');

        $emitter->addAction('postUpdate', function(RelationalTableGateway $gateway, $data) use ($cachePool) {
            if(isset($data[$gateway->primaryKeyFieldName])) {
                $cachePool->invalidateTags(['entity_'.$gateway->getTable().'_'.$data[$gateway->primaryKeyFieldName]]);
            }
        });

        $cacheTableTagInvalidator = function($tableName) use ($cachePool) {
            $cachePool->invalidateTags(['table_'.$tableName]);
        };

        foreach(['table.update:after', 'table.drop:after'] as $action) {
            $emitter->addAction($action, $cacheTableTagInvalidator);
        }

        $emitter->addAction('table.remove:after', function($tableName, $ids) use ($cachePool){
            foreach($ids as $id) {
                $cachePool->invalidateTags(['entity_'.$tableName.'_'.$id]);
            }
        });

        $emitter->addAction('table.update.directus_privileges:after', function ($data) use($cachePool) {
            $acl = Bootstrap::get('acl');
            $zendDb = Bootstrap::get('zendDb');
            $privileges = new DirectusPrivilegesTableGateway($zendDb, $acl);

            $record = $privileges->fetchById($data['id']);

            $cachePool->invalidateTags(['privilege_table_'.$record['table_name'].'_group_'.$record['group_id']]);
        });

        // /Cache subscriptions

        $emitter->addAction('application.error', function ($e) {
            $log = Bootstrap::get('log');
            $log->error($e);
        });

        $emitter->addFilter('response', function (Payload $payload) {
            $acl = Bootstrap::get('acl');

            if ($acl->isPublic()) {
                $payload->set('public', true);
            }

            return $payload;
        });

        $emitter->addAction('table.insert.directus_groups', function ($data) {
            $acl = Bootstrap::get('acl');
            $zendDb = Bootstrap::get('zendDb');
            $privilegesTable = new DirectusPrivilegesTableGateway($zendDb, $acl);

            $privilegesTable->insertPrivilege([
                'group_id' => $data['id'],
                'allow_view' => 1,
                'allow_add' => 0,
                'allow_edit' => 1,
                'allow_delete' => 0,
                'allow_alter' => 0,
                'table_name' => 'directus_users',
                'read_field_blacklist' => 'token',
                'write_field_blacklist' => 'group,token'
            ]);
        });

        $emitter->addFilter('table.insert:before', function (Payload $payload) {
            $tableName = $payload->attribute('tableName');
            $tableObject = TableSchema::getTableSchema($tableName);
            /** @var Acl $acl */
            $acl = Bootstrap::get('acl');

            if ($dateCreated = $tableObject->getDateCreateColumn()) {
                $payload[$dateCreated] = DateUtils::now();
            }

            if ($dateCreated = $tableObject->getDateUpdateColumn()) {
                $payload[$dateCreated] = DateUtils::now();
            }

            // Directus Users created user are themselves (primary key)
            // populating that field will be a duplicated primary key violation
            if ($tableName !== 'directus_users') {
                if ($userCreated = $tableObject->getUserCreateColumn()) {
                    $payload[$userCreated] = $acl->getUserId();
                }

                if ($userModified = $tableObject->getUserUpdateColumn()) {
                    $payload[$userModified] = $acl->getUserId();
                }
            }

            return $payload;
        }, Emitter::P_HIGH);

        $emitter->addFilter('table.update:before', function (Payload $payload) {
            $tableName = $payload->attribute('tableName');
            $tableObject = TableSchema::getTableSchema($tableName);
            /** @var Acl $acl */
            $acl = Bootstrap::get('acl');

            if ($dateModified = $tableObject->getDateUpdateColumn()) {
                $payload[$dateModified] = DateUtils::now();
            }

            if ($userModified = $tableObject->getUserUpdateColumn()) {
                $payload[$userModified] = $acl->getUserId();
            }

            // NOTE: exclude date_uploaded from updating a file record
            if ($payload->attribute('tableName') === 'directus_files') {
                $payload->remove('date_uploaded');
            }

            return $payload;
        }, Emitter::P_HIGH);

        $emitter->addFilter('table.insert:before', function (Payload $payload) {
            if ($payload->attribute('tableName') === 'directus_files') {
                $auth = Bootstrap::get('auth');
                $payload->remove('data');
                $payload->set('user', $auth->getUserInfo('id'));
            }

            return $payload;
        });

        $addFilesUrl = function ($rows) {
            foreach ($rows as &$row) {
                $config = Bootstrap::get('config');
                $fileURL = $config['filesystem']['root_url'];
                $thumbnailURL = $config['filesystem']['root_thumb_url'];
                $thumbnailFilenameParts = explode('.', $row['name']);
                $thumbnailExtension = array_pop($thumbnailFilenameParts);

                $row['url'] = $fileURL . '/' . $row['name'];
                if (Thumbnail::isNonImageFormatSupported($thumbnailExtension)) {
                    $thumbnailExtension = Thumbnail::defaultFormat();
                }

                $thumbnailFilename = $row['id'] . '.' . $thumbnailExtension;
                $row['thumbnail_url'] = $thumbnailURL . '/' . $thumbnailFilename;

                // filename-ext-100-100-true.jpg
                // @TODO: This should be another hook listener
                $filename = implode('.', $thumbnailFilenameParts);
                if (isset($row['type']) && $row['type'] == 'embed/vimeo') {
                    $oldThumbnailFilename = $row['name'] . '-vimeo-220-124-true.jpg';
                } else {
                    $oldThumbnailFilename = $filename . '-' . $thumbnailExtension . '-160-160-true.jpg';
                }

                // 314551321-vimeo-220-124-true.jpg
                // hotfix: there's not thumbnail for this file
                $row['old_thumbnail_url'] = $thumbnailURL . '/' . $oldThumbnailFilename;

                $embedManager = Bootstrap::get('embedManager');
                $provider = isset($row['type']) ? $embedManager->getByType($row['type']) : null;
                $row['html'] = null;
                if ($provider) {
                    $row['html'] = $provider->getCode($row);
                    $row['embed_url'] = $provider->getUrl($row);
                }
            }

            return $rows;
        };

        $emitter->addFilter('table.select.directus_files:before', function (Payload $payload) {
            $columns = $payload->get('columns');

            if (!in_array('name', $columns)) {
                $columns[] = 'name';
                $payload->set('columns', $columns);
            }

            return $payload;
        });

        // Add file url and thumb url
        $emitter->addFilter('table.select', function (Payload $payload) use ($addFilesUrl) {
            $selectState = $payload->attribute('selectState');
            $rows = $payload->getData();

            if ($selectState['table'] == 'directus_files') {
                $rows = $addFilesUrl($rows);
            } else if ($selectState['table'] === 'directus_messages') {
                $filesIds = [];
                foreach ($rows as &$row) {
                    if (!ArrayUtils::has($row, 'attachment')) {
                        continue;
                    }

                    $ids = array_filter(StringUtils::csv((string) $row['attachment'], true));
                    $row['attachment'] = ['data' => []];
                    foreach ($ids as  $id) {
                        $row['attachment']['data'][$id] = [];
                        $filesIds[] = $id;
                    }
                }

                $filesIds = array_filter($filesIds);
                if ($filesIds) {
                    $ZendDb = Bootstrap::get('zenddb');
                    $acl = Bootstrap::get('acl');
                    $table = new RelationalTableGateway('directus_files', $ZendDb, $acl);
                    $filesEntries = $table->loadItems([
                        'in' => ['id' => $filesIds]
                    ]);

                    $entries = [];
                    foreach($filesEntries as $id => $entry) {
                        $entries[$entry['id']] = $entry;
                    }

                    foreach ($rows as &$row) {
                        if (ArrayUtils::has($row, 'attachment') && $row['attachment']) {
                            foreach ($row['attachment']['data'] as $id => $attachment) {
                                $row['attachment']['data'][$id] = $entries[$id];
                            }

                            $row['attachment']['data'] = array_values($row['attachment']['data']);
                        }
                    }
                }
            }

            $payload->replace($rows);

            return $payload;
        });

        $emitter->addFilter('table.select.directus_users', function (Payload $payload) {
            $acl = Bootstrap::get('acl');
            $auth = Bootstrap::get('auth');
            $rows = $payload->getData();

            $userId = null;
            $groupId = null;
            if ($auth->loggedIn()) {
                $userId = $acl->getUserId();
                $groupId = $acl->getGroupId();
            }

            foreach ($rows as &$row) {
                $omit = [
                    'password',
                    'salt',
                ];

                // Authenticated user can see their private info
                // Admin can see all users private info
                if ($groupId !== 1 && $userId !== $row['id']) {
                    $omit = array_merge($omit, [
                        'token',
                        'access_token',
                        'reset_token',
                        'reset_expiration',
                        'email_messages',
                        'last_access',
                        'last_page'
                    ]);
                }

                $row = ArrayUtils::omit($row, $omit);
            }

            $payload->replace($rows);

            return $payload;
        });

        $hashUserPassword = function (Payload $payload) {
            if ($payload->has('password')) {
                $auth = Bootstrap::get('auth');
                $payload['salt'] = StringUtils::randomString();
                $payload['password'] = $auth->hashPassword($payload['password'], $payload['salt']);
            }

            return $payload;
        };

        $slugifyString = function ($insert, Payload $payload) {
            $tableName = $payload->attribute('tableName');
            $tableObject = TableSchema::getTableSchema($tableName);
            $data = $payload->getData();

            foreach ($tableObject->getColumns() as $column) {
                if ($column->getUI() !== 'slug') {
                    continue;
                }

                $parentColumnName = $column->getOptions('mirrored_field');
                if (!ArrayUtils::has($data, $parentColumnName)) {
                    continue;
                }

                $onCreationOnly = boolval($column->getOptions('only_on_creation'));
                if (!$insert && $onCreationOnly) {
                    continue;
                }

                $payload->set($column->getName(), slugify(ArrayUtils::get($data, $parentColumnName, '')));
            }

            return $payload;
        };

        $emitter->addFilter('table.insert:before', function (Payload $payload) use ($slugifyString) {
            return $slugifyString(true, $payload);
        });

        $emitter->addFilter('table.update:before', function (Payload $payload) use ($slugifyString) {
            return $slugifyString(false, $payload);
        });

        // TODO: Merge with hash user password
        $hashPasswordInterface = function (Payload $payload) {
            /** @var Provider $auth */
            $auth = Bootstrap::get('auth');
            $tableName = $payload->attribute('tableName');

            if (TableSchema::isSystemTable($tableName)) {
                return $payload;
            }

            $tableObject = TableSchema::getTableSchema($tableName);
            $data = $payload->getData();

            foreach ($data as $key => $value) {
                $columnObject = $tableObject->getColumn($key);

                if (!$columnObject) {
                    continue;
                }

                if ($columnObject->getUI() === 'password') {
                    // TODO: Use custom password hashing method
                    $payload->set($key, $auth->hashPassword($value));
                }
            }

            return $payload;
        };

        $emitter->addFilter('table.update.directus_users:before', function (Payload $payload) {
            $acl = Bootstrap::get('acl');
            $currentUserId = $acl->getUserId();

            if ($currentUserId != $payload->get('id')) {
                return $payload;
            }

            // ----------------------------------------------------------------------------
            // TODO: Add enforce method to ACL
            $adapter = Bootstrap::get('zendDb');
            $userTable = new BaseTableGateway('directus_users', $adapter);
            $groupTable = new BaseTableGateway('directus_groups', $adapter);

            $user = $userTable->find($payload->get('id'));
            $group = $groupTable->find($user['group']);

            if (!$group || !$acl->canEdit('directus_users')) {
                throw new ForbiddenException('you are not allowed to update your user information');
            }
            // ----------------------------------------------------------------------------

            return $payload;
        });
        $emitter->addFilter('table.insert.directus_users:before', $hashUserPassword);
        $emitter->addFilter('table.update.directus_users:before', $hashUserPassword);

        // Hash value to any non system table password interface column
        $emitter->addFilter('table.insert:before', $hashPasswordInterface);
        $emitter->addFilter('table.update:before', $hashPasswordInterface);

        $preventUsePublicGroup = function (Payload $payload) {
            $data = $payload->getData();

            if (!ArrayUtils::has($data, 'group')) {
                return $payload;
            }

            $groupId = ArrayUtils::get($data, 'group');
            if (is_array($groupId)) {
                $groupId = ArrayUtils::get($groupId, 'id');
            }

            if (!$groupId) {
                return $payload;
            }

            $zendDb = static::get('zendDb');
            $acl = static::get('acl');
            $tableGateway = new Database\TableGateway\BaseTableGateway('directus_groups', $zendDb, $acl);

            $row = $tableGateway->select(['id' => $groupId])->current();

            if (strtolower($row->name) == 'public') {
                throw new ForbiddenException(__t('exception_users_cannot_be_added_into_public_group'));
            }

            return $payload;
        };
        $emitter->addFilter('table.insert.directus_users:before', $preventUsePublicGroup);
        $emitter->addFilter('table.update.directus_users:before', $preventUsePublicGroup);

        $beforeSavingFiles = function ($payload) {
            $acl = Bootstrap::get('acl');
            $currentUserId = $acl->getUserId();

            // ----------------------------------------------------------------------------
            // TODO: Add enforce method to ACL
            $adapter = Bootstrap::get('zendDb');
            $userTable = new BaseTableGateway('directus_users', $adapter);
            $groupTable = new BaseTableGateway('directus_groups', $adapter);

            $user = $userTable->find($currentUserId);
            $group = $groupTable->find($user['group']);

            if (!$group || !$acl->canEdit('directus_files')) {
                throw new ForbiddenException('you are not allowed to upload, edit or delete files');
            }
            // ----------------------------------------------------------------------------

            return $payload;
        };

        $emitter->addAction('files.saving', $beforeSavingFiles);
        $emitter->addAction('files.thumbnail.saving', $beforeSavingFiles);
        // TODO: Make insert actions and filters
        $emitter->addFilter('table.insert.directus_files:before', $beforeSavingFiles);
        $emitter->addFilter('table.update.directus_files:before', $beforeSavingFiles);
        $emitter->addFilter('table.delete.directus_files:before', $beforeSavingFiles);

        return $emitter;
    }

    private static function session()
    {
        return new Session(new NativeSessionStorage());
    }

    private static function socialAuth()
    {
        return new Social();
    }
}
