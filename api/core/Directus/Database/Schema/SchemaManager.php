<?php

namespace Directus\Database\Schema;

use Directus\Database\Exception\TableNotFoundException;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\Object\Collection;
use Directus\Database\Schema\Sources\SchemaInterface;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;

class SchemaManager
{
    // Tables
    const TABLE_PERMISSIONS = 'directus_permissions';
    const TABLE_COLLECTIONS = 'directus_collections';
    const TABLE_FIELDS = 'directus_fields';
    const TABLE_FILES = 'directus_files';
    const TABLE_COLLECTION_PRESETS = 'directus_collection_presets';
    const TABLE_USERS = 'directus_users';

    /**
     * Schema source instance
     *
     * @var \Directus\Database\Schema\Sources\SchemaInterface
     */
    protected $source;

    /**
     * Schema data information
     *
     * @var array
     */
    protected $data = [];

    /**
     * System table prefix
     *
     * @var string
     */
    protected $prefix = 'directus_';

    /**
     * Directus System tables
     *
     * @var array
     */
    protected $directusTables = [
        'activity',
        'activity_read',
        'collections',
        'collection_presets',
        'fields',
        'files',
        'folders',
        'groups',
        'migrations',
        'permissions',
        'relations',
        'revisions',
        'settings',
        'users'
    ];

    public function __construct(SchemaInterface $source)
    {
        $this->source = $source;
    }

    /**
     * Adds a primary key to the given column
     *
     * @param $table
     * @param $column
     *
     * @return bool
     */
    public function addPrimaryKey($table, $column)
    {
        return $this->source->addPrimaryKey($table, $column);
    }

    /**
     * Removes the primary key of the given column
     *
     * @param $table
     * @param $column
     *
     * @return bool
     */
    public function dropPrimaryKey($table, $column)
    {
        return $this->source->dropPrimaryKey($table, $column);
    }

    /**
     * Get the table schema information
     *
     * @param string $tableName
     * @param array  $params
     * @param bool   $skipCache
     *
     * @throws TableNotFoundException
     *
     * @return \Directus\Database\Schema\Object\Collection
     */
    public function getTableSchema($tableName, $params = [], $skipCache = false)
    {
        $tableSchema = ArrayUtils::get($this->data, 'tables.' . $tableName, null);
        if (!$tableSchema || $skipCache) {
            // Get the table schema data from the source
            $tableResult = $this->source->getCollection($tableName);
            $tableData = $tableResult->current();

            if (!$tableData) {
                throw new TableNotFoundException($tableName);
            }

            // Create a table object based of the table schema data
            $tableSchema = $this->createTableObjectFromArray(array_merge($tableData, [
                'schema' => $this->source->getSchemaName()
            ]));
            $this->addTable($tableName, $tableSchema);
        }

        // =============================================================================
        // Set table columns
        // -----------------------------------------------------------------------------
        // @TODO: Do not allow to add duplicate column names
        // =============================================================================
        if (empty($tableSchema->getFields())) {
            $tableColumns = $this->getFields($tableName);
            $tableSchema->setFields($tableColumns);
        }

        return $tableSchema;
    }

    /**
     * Gets column schema
     *
     * @param $tableName
     * @param $columnName
     * @param bool $skipCache
     *
     * @return Field
     */
    public function getColumnSchema($tableName, $columnName, $skipCache = false)
    {
        $columnSchema = ArrayUtils::get($this->data, 'columns.' . $tableName . '.' . $columnName, null);

        if (!$columnSchema || $skipCache) {
            // Get the column schema data from the source
            $columnResult = $this->source->getColumns($tableName, ['column_name' => $columnName]);
            $columnData = $columnResult->current();

            // Create a column object based of the table schema data
            $columnSchema = $this->createColumnObjectFromArray($columnData);
            $this->addColumn($columnSchema);
        }

        return $columnSchema;
    }

    /**
     * Add the system table prefix to to a table name.
     *
     * @param string|array $tables
     *
     * @return string|array
     */
    public function addSystemTablePrefix($tables)
    {
        if (!is_array($tables)) {
            $tables = [$tables];
        }

        return array_map(function ($table) {
            // TODO: Directus tables prefix _probably_ will be dynamic
            return $this->prefix . $table;
        }, $tables);
    }

    /**
     * @deprecated 6.4.4 See addSystemTablesPrefix
     *
     * @param $tables
     *
     * @return array|string
     */
    public function addCoreTablePrefix($tables)
    {
        return $this->addSystemTablePrefix($tables);
    }

    /**
     * Get Directus System tables name
     *
     * @return array
     */
    public function getSystemTables()
    {
        return $this->addSystemTablePrefix($this->directusTables);
    }

    /**
     * @deprecated 6.4.4 See getSystemTables
     *
     * @return array
     */
    public function getCoreTables()
    {
        return $this->getSystemTables();
    }

    /**
     * Check if the given name is a system table
     *
     * @param $name
     *
     * @return bool
     */
    public function isSystemTables($name)
    {
        return in_array($name, $this->getSystemTables());
    }

    /**
     * Check if a table name exists
     *
     * @param $tableName
     * @return bool
     */
    public function tableExists($tableName)
    {
        return $this->source->collectionExists($tableName);
    }

    public function getTable($name)
    {
        return $this->source->getTable($name);
    }

    /**
     * Gets list of table
     *
     * @param array $params
     *
     * @return Collection[]
     */
    public function getCollections(array $params = [])
    {
        // TODO: Filter should be outsite
        // $schema = Bootstrap::get('schema');
        // $config = Bootstrap::get('config');

        // $ignoredTables = static::getDirectusTables(DirectusPreferencesTableGateway::$IGNORED_TABLES);
        // $blacklistedTable = $config['tableBlacklist'];
        // array_merge($ignoredTables, $blacklistedTable)
        $collections = $this->source->getCollections();

        $tables = [];
        foreach ($collections as $collection) {
            // Create a table object based of the table schema data
            $tableSchema = $this->createTableObjectFromArray(array_merge($collection, [
                'schema' => $this->source->getSchemaName()
            ]));
            $tableName = $tableSchema->getName();
            $this->addTable($tableName, $tableSchema);

            $tables[$tableName] = $tableSchema;
        }

        return $tables;
    }

    /**
     * Get all columns in the given table name
     *
     * @param $tableName
     * @param array $params
     *
     * @return \Directus\Database\Schema\Object\Field[]
     */
    public function getFields($tableName, $params = [])
    {
        // TODO: filter black listed fields
        // $acl = Bootstrap::get('acl');
        // $blacklist = $readFieldBlacklist = $acl->getTablePrivilegeList($tableName, $acl::FIELD_READ_BLACKLIST);

        $columnsSchema = ArrayUtils::get($this->data, 'columns.' . $tableName, null);
        if (!$columnsSchema) {
            $columnsResult = $this->source->getFields($tableName, $params);
            $relationsResult = $this->source->getRelations($tableName);

            // TODO: Improve this logic
            $relationsA = [];
            $relationsB = [];
            foreach ($relationsResult as $relation) {
                $relationsA[$relation['field_a']] = $relation;

                if (isset($relation['field_b'])) {
                    $relationsB[$relation['field_b']] = $relation;
                }
            }

            $columnsSchema = [];
            foreach ($columnsResult as $column) {
                $field = $this->createColumnObjectFromArray($column);

                if (array_key_exists($field->getName(), $relationsA)) {
                    $field->setRelationship($relationsA[$field->getName()]);
                } else if (array_key_exists($field->getName(), $relationsB)) {
                    $field->setRelationship($relationsB[$field->getName()]);
                }

                $columnsSchema[] = $field;
            }

            $this->data['columns'][$tableName] = $columnsSchema;
        }

        return $columnsSchema;
    }

    public function getColumnsName($tableName)
    {
        $columns = $this->getColumns($tableName);

        $columnNames = [];
        foreach ($columns as $column) {
            $columnNames[] = $column->getName();
        }

        return $columnNames;
    }

    /**
     * Get all the columns
     *
     * @return Field[]
     */
    public function getAllFields()
    {
        $allColumns = $this->source->getAllFields();

        $columns = [];
        foreach($allColumns as $column) {
            $columns[] = $this->createColumnObjectFromArray($column);
        }

        return $columns;
    }

    /**
     * Get a list of columns table grouped by table name
     *
     * @return array
     */
    public function getAllFieldsByTable()
    {
        $fields = [];
        foreach ($this->getAllFields() as $field) {
            $collectionName = $field->getCollectionName();
            if (!isset($fields[$collectionName])) {
                $fields[$collectionName] = [];
            }

            $columns[$collectionName][] = $field;
        }

        return $fields;
    }

    public function getPrimaryKey($tableName)
    {
        $collection = $this->getTableSchema($tableName);
        if ($collection) {
            return $collection->getPrimaryKeyName();
        }

        return false;
    }

    public function hasSystemDateColumn($tableName)
    {
        $tableObject = $this->getTableSchema($tableName);

        return $tableObject->getDateCreateField() || $tableObject->getDateUpdateField();
    }

    public function castRecordValues($records, $columns)
    {
        return $this->source->castRecordValues($records, $columns);
    }

    /**
     * Cast value against a database type
     *
     * NOTE: it only works with MySQL data types
     *
     * @param $value
     * @param $type
     * @param $length
     *
     * @return mixed
     */
    public function castValue($value, $type = null, $length = false)
    {
        return $this->source->castValue($value, $type, $length);
    }

    /**
     * Checks whether the given type is numeric type
     *
     * @param $type
     *
     * @return bool
     */
    public function isNumericType($type)
    {
        return $this->source->isNumericType($type);
    }

    /**
     * Checks whether the given type is string type
     *
     * @param $type
     *
     * @return bool
     */
    public function isStringType($type)
    {
        return $this->source->isStringType($type);
    }

    /**
     * Checks whether the given type is integer type
     *
     * @param $type
     *
     * @return bool
     */
    public function isIntegerType($type)
    {
        return $this->source->isIntegerType($type);
    }

    /**
     * Checks whether the given type is decimal type
     *
     * @param $type
     *
     * @return bool
     */
    public function isDecimalType($type)
    {
        return $this->source->isDecimalType($type);
    }

    /**
     * Cast default value
     *
     * @param $value
     * @param $type
     * @param $length
     *
     * @return mixed
     */
    public function castDefaultValue($value, $type, $length = null)
    {
        if (strtolower($value) === 'null') {
            $value = null;
        } else {
            $value = $this->castValue($value, $type, $length);
        }

        return $value;
    }

    /**
     * Get all Directus system tables name
     *
     * @param array $filterNames
     *
     * @return array
     */
    public function getDirectusTables(array $filterNames = [])
    {
        $tables = $this->directusTables;
        if ($filterNames) {
            foreach ($tables as $i => $table) {
                if (!in_array($table, $filterNames)) {
                    unset($tables[$i]);
                }
            }
        }

        return $this->addSystemTablePrefix($tables);
    }

    /**
     * Check if a given table is a directus system table name
     *
     * @param $tableName
     *
     * @return bool
     */
    public function isDirectusTable($tableName)
    {
        return in_array($tableName, $this->getDirectusTables());
    }

    /**
     * Get the schema adapter
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->source;
    }

    /**
     * List of supported databases
     *
     * @return array
     */
    public static function getSupportedDatabases()
    {
        return [
            'mysql' => [
                'id' => 'mysql',
                'name' => 'MySQL/Percona'
            ],
        ];
    }

    public static function getTemplates()
    {
        // @TODO: SchemaManager shouldn't be a class with static methods anymore
        // the UI templates list will be provided by a container or bootstrap.
        $path = implode(DIRECTORY_SEPARATOR, [
            BASE_PATH,
            'api',
            'migrations',
            'templates',
            '*'
        ]);

        $templatesDirs = glob($path, GLOB_ONLYDIR);
        $templatesData = [];
        foreach ($templatesDirs as $dir) {
            $key = basename($dir);
            $templatesData[$key] = [
                'id' => $key,
                'name' => uc_convert($key)
            ];
        }

        return $templatesData;
    }

    /**
     * Gets a collection object from an array attributes data
     * @param $data
     *
     * @return Collection
     */
    public function createTableObjectFromArray($data)
    {
        return new Collection($data);
    }

    /**
     * Adds fixed core table columns information such as system columns name
     *
     * @param array $column
     *
     * @return array
     */
    public function parseSystemTablesColumn(array $column)
    {
        $tableName = ArrayUtils::get($column, 'table_name');
        $columnName = ArrayUtils::get($column, 'column_name');

        if (!StringUtils::startsWith($tableName, $this->prefix)) {
            return $column;
        }

        // Status
        $hasStatus = in_array($tableName, $this->getDirectusTables(['users', 'files']));
        if ($columnName == 'status' && $hasStatus) {
            $column['ui'] = static::INTERFACE_STATUS;
        }

        return $column;
    }

    /**
     * @deprecated 6.4.4 See parseSystemTablesColumn
     *
     * @param array $column
     *
     * @return array
     */
    public function parseCoreTablesColumn(array $column)
    {
        return $this->parseSystemTablesColumn($column);
    }

    /**
     * Creates a column object from the given array
     *
     * @param array $column
     *
     * @return Field
     */
    public function createColumnObjectFromArray($column)
    {
        if (!isset($column['interface'])) {
            $column['interface'] = $this->getFieldDefaultInterface($column['type']);
        }

        $column = $this->parseSystemTablesColumn($column);

        $options = json_decode(isset($column['options']) ? $column['options'] : '', true);
        $column['options'] = $options ? $options : [];

        // $isSystemColumn = $this->isSystemField($column['interface']);
        // $column['system'] = $isSystemColumn;

        // PRIMARY KEY must be required
        if ($column['key'] === 'PRI') {
            $column['required'] = true;
        }

        // NOTE: Alias column must are nullable
        if ($column['type'] === 'ALIAS') {
            $column['nullable'] = 1;
        }

        // NOTE: MariaDB store "NULL" as a string on some data types such as VARCHAR.
        // We reserved the word "NULL" on nullable data type to be actually null
        if ($column['nullable'] === 1 && $column['default_value'] == 'NULL') {
            $column['default_value'] = null;
        }

        $columnObject = new Field($column);
        // if (isset($column['collection_b'])) {
        //     // var_dump($column);
        //     $columnObject->setRelationship([
        //         'type' => ArrayUtils::get($column, 'relationship_type'),
        //         'related_table' => ArrayUtils::get($column, 'collection_b'),
        //         'junction_table' => ArrayUtils::get($column, 'store_collection'),
        //         'junction_key_right' => ArrayUtils::get($column, 'store_key_a'),
        //         'junction_key_left' => ArrayUtils::get($column, 'store_key_b'),
        //     ]);
        // }

        return $columnObject;
    }

    /**
     * Checks whether the interface is a system interface
     *
     * @param $interface
     *
     * @return bool
     */
    public function isSystemField($interface)
    {
        return SystemInterface::isSystem($interface);
    }

    /**
     * Checks whether the interface is primary key interface
     *
     * @param $interface
     *
     * @return bool
     */
    public function isPrimaryKeyInterface($interface)
    {
        return $interface === SystemInterface::INTERFACE_PRIMARY_KEY;
    }

    protected function addTable($name, $schema)
    {
        // save the column into the data
        // @NOTE: this is the early implementation of cache
        // soon this will be change to cache
        $this->data['tables'][$name] = $schema;
    }

    protected function addColumn(Field $column)
    {
        $tableName = $column->getTableName();
        $columnName = $column->getName();
        $this->data['columns'][$tableName][$columnName] = $column;
    }

    /**
     * Gets the data types default interfaces
     *
     * @return array
     */
    public function getDefaultInterfaces()
    {
        return $this->source->getDefaultInterfaces();
    }

    /**
     * Gets the given data type default interface
     *
     * @param $type
     *
     * @return string
     */
    public function getFieldDefaultInterface($type)
    {
        return $this->source->getColumnDefaultInterface($type);
    }

    /**
     *
     *
     * @param $type
     *
     * @return integer
     */
    public function getFieldDefaultLength($type)
    {
        return $this->source->getColumnDefaultLength($type);
    }

    /**
     * Gets the source schema adapter
     *
     * @return SchemaInterface
     */
    public function getSource()
    {
        return $this->source;
    }
}
