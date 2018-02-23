<?php

namespace Directus\Application;

use Cache\Adapter\Apc\ApcCachePool;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Adapter\Common\PhpCachePool;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use Cache\Adapter\Memcached\MemcachedCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Adapter\Redis\RedisCachePool;
use Cache\Adapter\Void\VoidCachePool;
use Directus\Application\ErrorHandlers\ErrorHandler;
use Directus\Authentication\Provider;
use Directus\Authentication\User\Provider\UserTableGatewayProvider;
use Directus\Cache\Response;
use Directus\Database\Connection;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\Object\Collection;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\SchemaFactory;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\DirectusPermissionsTableGateway;
use Directus\Database\TableGateway\DirectusSettingsTableGateway;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Database\TableSchema;
use Directus\Embed\EmbedManager;
use Directus\Exception\Http\ForbiddenException;
use Directus\Filesystem\Files;
use Directus\Filesystem\Filesystem;
use Directus\Filesystem\FilesystemFactory;
use Directus\Filesystem\Thumbnail;
use Directus\Hash\HashManager;
use Directus\Hook\Emitter;
use Directus\Hook\Payload;
use Directus\Permissions\Acl;
use Directus\Services\AuthService;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;
use Directus\Util\StringUtils;
use League\Flysystem\Adapter\Local;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Zend\Db\TableGateway\TableGateway;

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
        $container['schema_factory']    = $this->getSchemaFactory();
        $container['hash_manager']      = $this->getHashManager();
        $container['embed_manager']     = $this->getEmbedManager();
        $container['filesystem']        = $this->getFileSystem();
        $container['files']             = $this->getFiles();

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
            // TODO: Move log configuration outside "slim app" settings
            $path = $container->get('path_base') . '/logs';
            $config = $container->get('config');
            if ($config->has('settings.logger.path')) {
                $path = $config->get('settings.logger.path');
            }

            $handler = new StreamHandler(
                $path . '/debug.' . date('Y-m') . '.log',
                Logger::DEBUG,
                false
            );

            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            $handler = new StreamHandler(
                $path . '/error.' . date('Y-m') . '.log',
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
         * @param Container $container
         *
         * @return ErrorHandler
         */
        $errorHandler = function (Container $container) {
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
        return function (Container $container) {
            $emitter = new Emitter();
            $cachePool = $container->get('cache');

            // TODO: Move this separately, this is temporary while we move things around
            $emitter->addFilter('load.relational.onetomany', function (Payload $payload) {
                $rows = $payload->getData();
                /** @var Field $column */
                $column = $payload->attribute('column');

                if ($column->getInterface() !== 'translation') {
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

            // Cache subscriptions
            $emitter->addAction('postUpdate', function (RelationalTableGateway $gateway, $data) use ($cachePool) {
                if(isset($data[$gateway->primaryKeyFieldName])) {
                    $cachePool->invalidateTags(['entity_'.$gateway->getTable().'_'.$data[$gateway->primaryKeyFieldName]]);
                }
            });

            $cacheTableTagInvalidator = function ($tableName) use ($cachePool) {
                $cachePool->invalidateTags(['table_'.$tableName]);
            };

            foreach (['table.update:after', 'table.drop:after'] as $action) {
                $emitter->addAction($action, $cacheTableTagInvalidator);
            }

            $emitter->addAction('table.remove:after', function ($tableName, $ids) use ($cachePool){
                foreach ($ids as $id) {
                    $cachePool->invalidateTags(['entity_'.$tableName.'_'.$id]);
                }
            });

            $emitter->addAction('table.update.directus_privileges:after', function ($data) use($container, $cachePool) {
                $acl = $container->get('acl');
                $dbConnection = $container->get('database');
                $privileges = new DirectusPrivilegesTableGateway($dbConnection, $acl);
                $record = $privileges->fetchById($data['id']);
                $cachePool->invalidateTags(['privilege_table_'.$record['table_name'].'_group_'.$record['group_id']]);
            });
            // /Cache subscriptions

            $emitter->addAction('application.error', function ($e) use($container) {
                /** @var \Throwable|\Exception $exception */
                $exception = $e;
                /** @var Logger $logger */
                $logger = $container->get('logger');

                $logger->error($exception->getMessage());
            });
            $emitter->addFilter('response', function (Payload $payload) use ($container) {
                /** @var Acl $acl */
                $acl = $container->get('acl');
                if ($acl->isPublic() || !$acl->getUserId()) {
                    $payload->set('public', true);
                }
                return $payload;
            });
            $emitter->addAction('table.delete', function ($tableName, $id) use ($container) {
                /** @var Acl $acl */
                $acl = $container->get('acl');
                $db = $container->get('database');

                $parentLogEntry = BaseRowGateway::makeRowGatewayFromTableName(
                    'id',
                    'directus_activity',
                    $db
                );

                $logData = [
                    'type' => DirectusActivityTableGateway::makeLogTypeFromTableName($tableName),
                    'action' => DirectusActivityTableGateway::ACTION_DELETE,
                    'user' => $acl->getUserId(),
                    'datetime' => DateUtils::now(),
                    'ip' => get_request_ip(),
                    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                    'collection' => $tableName,
                    'item' => $id,
                    'message' => null
                    // TODO: Move to revisions
                    // 'parent_id' => null,
                    // 'data' => json_encode($fullRecordData),
                    // 'delta' => json_encode($deltaRecordData),
                    // 'parent_changed' => (int)$parentRecordChanged,
                    // 'identifier' => $recordIdentifier,
                ];
                $parentLogEntry->populate($logData, false);
                $parentLogEntry->save();
            });
            $emitter->addAction('table.insert.directus_groups', function ($data) use ($container) {
                $acl = $container->get('acl');
                $zendDb = $container->get('database');
                $privilegesTable = new DirectusPermissionsTableGateway($zendDb, $acl);
                $privilegesTable->insertPrivilege([
                    'group' => $data['id'],
                    'collection' => 'directus_users',
                    'create' => 0,
                    'read' => 1,
                    'update' => 1,
                    'delete' => 0,
                    'read_field_blacklist' => 'token',
                    'write_field_blacklist' => 'group,token'
                ]);
            });
            $emitter->addFilter('table.insert:before', function (Payload $payload) use ($container) {
                $tableName = $payload->attribute('tableName');
                $tableObject = TableSchema::getTableSchema($tableName);
                /** @var Acl $acl */
                $acl = $container->get('acl');


                if ($dateCreated = $tableObject->getDateCreateField()) {
                    $payload[$dateCreated] = DateUtils::now();
                }

                if ($dateCreated = $tableObject->getDateUpdateField()) {
                    $payload[$dateCreated] = DateUtils::now();
                }

                // Directus Users created user are themselves (primary key)
                // populating that field will be a duplicated primary key violation
                if ($tableName === 'directus_users') {
                    return $payload;
                }

                $userCreated = $tableObject->getUserCreateField();
                $userModified = $tableObject->getUserUpdateField();

                if ($userCreated && (!$payload->has($userCreated) || !$acl->isAdmin())) {
                    $payload[$userCreated] = $acl->getUserId();
                }

                if ($userModified && (!$payload->has($userModified) || !$acl->isAdmin())) {
                    $payload[$userModified] = $acl->getUserId();
                }

                return $payload;
            }, Emitter::P_HIGH);
            $emitter->addFilter('table.update:before', function (Payload $payload) use ($container) {
                $tableName = $payload->attribute('tableName');
                $tableObject = TableSchema::getTableSchema($tableName);
                /** @var Acl $acl */
                $acl = $container->get('acl');
                if ($dateModified = $tableObject->getDateUpdateField()) {
                    $payload[$dateModified] = DateUtils::now();
                }
                if ($userModified = $tableObject->getUserUpdateField()) {
                    $payload[$userModified] = $acl->getUserId();
                }
                // NOTE: exclude date_uploaded from updating a file record
                if ($payload->attribute('tableName') === 'directus_files') {
                    $payload->remove('date_uploaded');
                }
                return $payload;
            }, Emitter::P_HIGH);
            $emitter->addFilter('table.insert:before', function (Payload $payload) use ($container) {
                if ($payload->attribute('tableName') === 'directus_files') {
                    /** @var Acl $auth */
                    $acl = $container->get('acl');
                    $data = $payload->getData();

                    /** @var \Directus\Filesystem\Files $files */
                    $files = $container->get('files');

                    if (array_key_exists('data', $data) && filter_var($data['data'], FILTER_VALIDATE_URL)) {
                        $dataInfo = $files->getLink($data['data']);
                    } else {
                        $dataInfo = $files->getDataInfo($data['data']);
                    }

                    $type = ArrayUtils::get($dataInfo, 'type', ArrayUtils::get($data, 'type'));

                    if (strpos($type, 'embed/') === 0) {
                        $recordData = $files->saveEmbedData($dataInfo);
                    } else {
                        $recordData = $files->saveData($payload['data'], $payload['filename']);
                    }

                    $payload->replace(array_merge($recordData, ArrayUtils::omit($data, 'filename')));
                    $payload->remove('data');
                    $payload->set('upload_user', $acl->getUserId());
                    $payload->set('upload_date', DateUtils::now());
                }

                return $payload;
            });
            $addFilesUrl = function ($rows) use ($container) {
                foreach ($rows as &$row) {
                    $config = $container->get('config');
                    $fileURL = $config['filesystem']['root_url'];
                    $thumbnailURL = $config['filesystem']['root_thumb_url'];
                    $thumbnailFilenameParts = explode('.', $row['filename']);
                    $thumbnailExtension = array_pop($thumbnailFilenameParts);
                    $row['url'] = $fileURL . '/' . $row['filename'];
                    if (Thumbnail::isNonImageFormatSupported($thumbnailExtension)) {
                        $thumbnailExtension = Thumbnail::defaultFormat();
                    }
                    $thumbnailFilename = $row['id'] . '.' . $thumbnailExtension;
                    $row['thumbnail_url'] = $thumbnailURL . '/' . $thumbnailFilename;
                    // filename-ext-100-100-true.jpg
                    // @TODO: This should be another hook listener
                    $filename = implode('.', $thumbnailFilenameParts);
                    if (isset($row['type']) && $row['type'] == 'embed/vimeo') {
                        $oldThumbnailFilename = $row['filename'] . '-vimeo-220-124-true.jpg';
                    } else {
                        $oldThumbnailFilename = $filename . '-' . $thumbnailExtension . '-160-160-true.jpg';
                    }
                    // 314551321-vimeo-220-124-true.jpg
                    // hotfix: there's not thumbnail for this file
                    $row['old_thumbnail_url'] = $thumbnailURL . '/' . $oldThumbnailFilename;
                    $embedManager = $container->get('embed_manager');
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
                if (!in_array('filename', $columns)) {
                    $columns[] = 'filename';
                    $payload->set('columns', $columns);
                }
                return $payload;
            });

            // -- Data types -----------------------------------------------------------------------------
            $parseArray = function ($collection, $data) use ($container) {
                /** @var SchemaManager $schemaManager */
                $schemaManager = $container->get('schema_manager');
                $collectionObject = $schemaManager->getTableSchema($collection);

                foreach ($collectionObject->getFields(array_keys($data)) as $field) {
                    if (!$field->isArray()) {
                        continue;
                    }

                    $key = $field->getName();
                    $value = $data[$key];

                    // NOTE: If the array has value with comma it will be treat as a separate value
                    // should we encode the commas to "hide" the comma when splitting the values?
                    if (is_array($value)) {
                        $value = serialize($value);
                    } else {
                        $value = unserialize($value);
                    }

                    $data[$key] = $value;
                }

                return $data;
            };
            $emitter->addFilter('table.insert:before', function (Payload $payload) use ($parseArray) {
                $payload->replace($parseArray($payload->attribute('tableName'), $payload->getData()));

                return $payload;
            });
            $emitter->addFilter('table.update:before', function (Payload $payload) use ($parseArray) {
                $payload->replace($parseArray($payload->attribute('tableName'), $payload->getData()));

                return $payload;
            });
            $emitter->addFilter('table.select', function (Payload $payload) use ($parseArray) {
                $rows = $payload->getData();
                $collection = $payload->attribute('tableName');

                foreach ($rows as $key => $row) {
                    $rows[$key] = $parseArray($collection, $row);
                }

                $payload->replace($rows);

                return $payload;
            });

            $parseJson = function ($collection, $data) use ($container) {
                /** @var SchemaManager $schemaManager */
                $schemaManager = $container->get('schema_manager');
                $collectionObject = $schemaManager->getTableSchema($collection);

                foreach ($collectionObject->getFields(array_keys($data)) as $field) {
                    if (!$field->isJson()) {
                        continue;
                    }

                    $key = $field->getName();
                    $value = $data[$key];

                    // NOTE: If the array has value with comma it will be treat as a separate value
                    // should we encode the commas to "hide" the comma when splitting the values?
                    if (is_string($value)) {
                        $value = unserialize($value);
                    } else if ($value) {
                        $value = serialize($value);
                    }

                    $data[$key] = $value;
                }

                return $data;
            };
            $emitter->addFilter('table.insert:before', function (Payload $payload) use ($parseJson) {
                $payload->replace($parseJson($payload->attribute('tableName'), $payload->getData()));

                return $payload;
            });
            $emitter->addFilter('table.update:before', function (Payload $payload) use ($parseJson) {
                $payload->replace($parseJson($payload->attribute('tableName'), $payload->getData()));

                return $payload;
            });
            $emitter->addFilter('table.select', function (Payload $payload) use ($parseJson) {
                $rows = $payload->getData();
                $collection = $payload->attribute('tableName');

                foreach ($rows as $key => $row) {
                    $rows[$key] = $parseJson($collection, $row);
                }

                $payload->replace($rows);

                return $payload;
            });
            // -------------------------------------------------------------------------------------------
            // Add file url and thumb url
            $emitter->addFilter('table.select', function (Payload $payload) use ($addFilesUrl, $container) {
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
                        $ZendDb = $container->get('database');
                        $acl = $container->get('acl');
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
            $emitter->addFilter('table.select.directus_users', function (Payload $payload) use ($container) {
                $acl = $container->get('acl');
                $rows = $payload->getData();
                $userId = $acl->getUserId();
                $groupId = $acl->getGroupId();
                foreach ($rows as &$row) {
                    $omit = [
                        'password'
                    ];
                    // Authenticated user can see their private info
                    // Admin can see all users private info
                    if ($groupId !== 1 && $userId !== $row['id']) {
                        $omit = array_merge($omit, [
                            'token',
                            'email_notifications',
                            'last_access',
                            'last_page'
                        ]);
                    }
                    $row = ArrayUtils::omit($row, $omit);
                }
                $payload->replace($rows);
                return $payload;
            });
            $hashUserPassword = function (Payload $payload) use ($container) {
                if ($payload->has('password')) {
                    $auth = $container->get('auth');
                    $payload['password'] = $auth->hashPassword($payload['password']);
                }
                return $payload;
            };
            $slugifyString = function ($insert, Payload $payload) {
                $tableName = $payload->attribute('tableName');
                $tableObject = TableSchema::getTableSchema($tableName);
                $data = $payload->getData();
                foreach ($tableObject->getFields() as $column) {
                    if ($column->getInterface() !== 'slug') {
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
            $onInsertOrUpdate = function (Payload $payload) use ($container) {
                /** @var Provider $auth */
                $auth = $container->get('auth');
                $tableName = $payload->attribute('tableName');

                if (TableSchema::isSystemTable($tableName)) {
                    return $payload;
                }

                $tableObject = TableSchema::getTableSchema($tableName);
                $data = $payload->getData();
                foreach ($data as $key => $value) {
                    $columnObject = $tableObject->getField($key);
                    if (!$columnObject) {
                        continue;
                    }
                    if ($columnObject->getInterface() === 'password') {
                        // TODO: Use custom password hashing method
                        $payload->set($key, $auth->hashPassword($value));
                    }
                }
                return $payload;
            };
            $emitter->addFilter('table.update.directus_users:before', function (Payload $payload) use ($container) {
                $acl = $container->get('acl');
                $currentUserId = $acl->getUserId();
                if ($currentUserId != $payload->get('id')) {
                    return $payload;
                }
                // ----------------------------------------------------------------------------
                // TODO: Add enforce method to ACL
                $adapter = $container->get('database');
                $userTable = new BaseTableGateway('directus_users', $adapter);
                $groupTable = new BaseTableGateway('directus_groups', $adapter);
                $user = $userTable->find($payload->get('id'));
                $group = $groupTable->find($user['group']);
                if (!$group || !$acl->canUpdate('directus_users')) {
                    throw new ForbiddenException('you are not allowed to update your user information');
                }
                // ----------------------------------------------------------------------------
                return $payload;
            });
            $emitter->addFilter('table.insert.directus_users:before', $hashUserPassword);
            $emitter->addFilter('table.update.directus_users:before', $hashUserPassword);
            // Hash value to any non system table password interface column
            $emitter->addFilter('table.insert:before', $onInsertOrUpdate);
            $emitter->addFilter('table.update:before', $onInsertOrUpdate);
            $preventUsePublicGroup = function (Payload $payload) use ($container) {
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
                $zendDb = $container->get('database');
                $acl = $container->get('acl');
                $tableGateway = new BaseTableGateway('directus_groups', $zendDb, $acl);
                $row = $tableGateway->select(['id' => $groupId])->current();
                if (strtolower($row->name) == 'public') {
                    throw new ForbiddenException(__t('exception_users_cannot_be_added_into_public_group'));
                }
                return $payload;
            };
            $emitter->addFilter('table.insert.directus_users:before', $preventUsePublicGroup);
            $emitter->addFilter('table.update.directus_users:before', $preventUsePublicGroup);
            $beforeSavingFiles = function ($payload) use ($container) {
                $acl = $container->get('acl');
                $currentUserId = $acl->getUserId();
                // ----------------------------------------------------------------------------
                // TODO: Add enforce method to ACL
                $adapter = $container->get('database');
                $userTable = new BaseTableGateway('directus_users', $adapter);
                $groupTable = new BaseTableGateway('directus_groups', $adapter);
                $user = $userTable->find($currentUserId);
                $group = $groupTable->find($user['group']);
                if (!$group || !$acl->canUpdate('directus_files')) {
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
            $db = $container->get('database');

            return new Provider(
                new UserTableGatewayProvider(
                    new DirectusUsersTableGateway($db)
                ),
                [
                    'secret_key' => $container->get('config')->get('auth.secret_key')
                ]
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

            /** @var Collection[] $tables */
            // $tables = TableSchema::getTablesSchema([
            //     'include_columns' => true
            // ], true);
            //
            // $magicOwnerColumnsByTable = [];
            // foreach ($tables as $table) {
            //     $column = $table->getUserCreateField();
            //
            //     if ($column) {
            //         $magicOwnerColumnsByTable[$table->getName()] = $table->getUserCreateField();
            //     }
            // }

            // TODO: Move this to a method
            // $acl::$cms_owner_columns_by_table = array_merge($magicOwnerColumnsByTable, $acl::$cms_owner_columns_by_table);
            if ($auth->check()) {
                $privilegesTable = new DirectusPermissionsTableGateway($dbConnection, $acl);
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
                    return new \Directus\Database\Schema\Sources\MySQLSchema($adapter);
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
    protected function getSchemaFactory()
    {
        return function (Container $container) {
            return new SchemaFactory(
                $container->get('schema_manager')
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
            $basePath = $container->get('path_base');

            $path = implode(DIRECTORY_SEPARATOR, [
                $basePath,
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

    protected function getFileSystem()
    {
        return function (Container $container) {
            $config = $container->get('config');

            return new Filesystem(FilesystemFactory::createAdapter($config->get('filesystem')));
        };
    }

    /**
     * @return \Closure
     */
    protected function getFiles()
    {
        return function (Container $container) {
            $container['settings.files'] = function () use ($container) {
                $dbConnection = $container->get('database');
                $settingsTable = new TableGateway('directus_settings', $dbConnection);

                return $settingsTable->select([
                    'scope' => 'files'
                ])->toArray();
            };

            $filesystem = $container->get('filesystem');
            $config = $container->get('config');
            $config = $config->get('filesystem', []);
            $settings = $container->get('settings.files');
            $emitter = $container->get('hook_emitter');

            return new Files($filesystem, $config, $settings, $emitter);
        };
    }

    protected function getEmbedManager()
    {
        return function (Container $container) {
            $app = Application::getInstance();
            $embedManager = new EmbedManager();
            $acl = $container->get('acl');
            $adapter = $container->get('database');

            // Fetch files settings
            $settingsTableGateway = new DirectusSettingsTableGateway($adapter, $acl);
            try {
                $settings = $settingsTableGateway->loadItems([
                    'filter' => ['scope' => 'files']
                ]);
            } catch (\Exception $e) {
                $settings = [];
                /** @var Logger $logger */
                $logger = $container->get('logger');
                $logger->warning($e->getMessage());
            }

            $providers = [
                '\Directus\Embed\Provider\VimeoProvider',
                '\Directus\Embed\Provider\YoutubeProvider'
            ];

            $path = implode(DIRECTORY_SEPARATOR, [
                $app->getContainer()->get('path_base'),
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
        };
    }

    /**
     * Register all services
     *
     * @param Container $mainContainer
     *
     * @return \Closure
     *
     * @internal param Container $container
     *
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

