<?php

namespace Directus\Database\Schema;

use Directus\Database\Exception\CollectionNotFoundException;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\Object\Collection;
use Directus\Database\Schema\Sources\SchemaInterface;
use Directus\Util\ArrayUtils;

class SchemaManager
{
    // Tables
    const COLLECTION_ACTIVITY            = 'directus_activity';
    const COLLECTION_ACTIVITY_SEEN       = 'directus_activity_seen';
    const COLLECTION_COLLECTIONS         = 'directus_collections';
    const COLLECTION_COLLECTION_PRESETS  = 'directus_collection_presets';
    const COLLECTION_FIELDS              = 'directus_fields';
    const COLLECTION_FILES               = 'directus_files';
    const COLLECTION_FOLDERS             = 'directus_folders';
    const COLLECTION_MIGRATIONS          = 'directus_migrations';
    const COLLECTION_ROLES               = 'directus_roles';
    const COLLECTION_PERMISSIONS         = 'directus_permissions';
    const COLLECTION_RELATIONS           = 'directus_relations';
    const COLLECTION_REVISIONS           = 'directus_revisions';
    const COLLECTION_SETTINGS            = 'directus_settings';
    const COLLECTION_USER_ROLES          = 'directus_user_roles';
    const COLLECTION_USERS               = 'directus_users';

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
        // FIXME: Use constant value instead (one place)
        'activity',
        'activity_read',
        'collection_presets',
        'collections',
        'fields',
        'files',
        'folders',
        'migrations',
        'permissions',
        'relations',
        'revisions',
        'roles',
        'settings',
        'user_roles',
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
     * @throws CollectionNotFoundException
     *
     * @return \Directus\Database\Schema\Object\Collection
     */
    public function getCollection($collectionName, $params = [], $skipCache = false)
    {
        $collection = ArrayUtils::get($this->data, 'collections.' . $collectionName, null);
        if (!$collection || $skipCache) {
            // Get the table schema data from the source
            $collectionResult = $this->source->getCollection($collectionName);
            $collectionData = $collectionResult->current();

            if (!$collectionData) {
                throw new CollectionNotFoundException($collectionName);
            }

            // Create a table object based of the table schema data
            $collection = $this->createCollectionFromArray(array_merge($collectionData, [
                'schema' => $this->source->getSchemaName()
            ]));
            $this->addCollection($collectionName, $collection);
        }

        // =============================================================================
        // Set table columns
        // -----------------------------------------------------------------------------
        // @TODO: Do not allow to add duplicate column names
        // =============================================================================
        if (empty($collection->getFields())) {
            $fields = $this->getFields($collectionName, [], $skipCache);
            $collection->setFields($fields);
        }

        return $collection;
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
    public function getField($tableName, $columnName, $skipCache = false)
    {
        $columnSchema = ArrayUtils::get($this->data, 'fields.' . $tableName . '.' . $columnName, null);

        if (!$columnSchema || $skipCache) {
            // Get the column schema data from the source
            $columnResult = $this->source->getFields($tableName, ['column_name' => $columnName]);
            $columnData = $columnResult->current();

            // Create a column object based of the table schema data
            $columnSchema = $this->createFieldFromArray($columnData);
            $this->addField($columnSchema);
        }

        return $columnSchema;
    }

    /**
     * Check if the given name is a system table
     *
     * @param $name
     *
     * @return bool
     */
    public function isSystemCollection($name)
    {
        return in_array($name, $this->getSystemCollections());
    }

    /**
     * Check if a collection exists
     *
     * @param string $collectionName
     *
     * @return bool
     */
    public function collectionExists($collectionName)
    {
        return $this->source->collectionExists($collectionName);
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
            $tableSchema = $this->createCollectionFromArray(array_merge($collection, [
                'schema' => $this->source->getSchemaName()
            ]));
            $tableName = $tableSchema->getName();
            $this->addCollection($tableName, $tableSchema);

            $tables[$tableName] = $tableSchema;
        }

        return $tables;
    }

    /**
     * Returns a list of all collections names
     *
     * @param array $params
     *
     * @return array
     */
    public function getCollectionsName(array $params = [])
    {
        $names = [];
        foreach ($this->getCollections($params) as $collection) {
            $names[] = $collection->getName();
        }

        return $names;
    }

    /**
     * Get all columns in the given table name
     *
     * @param $tableName
     * @param array $params
     * @param bool $skipCache
     *
     * @return \Directus\Database\Schema\Object\Field[]
     */
    public function getFields($tableName, $params = [], $skipCache = false)
    {
        // TODO: filter black listed fields on services level

        $columnsSchema = ArrayUtils::get($this->data, 'columns.' . $tableName, null);
        if (!$columnsSchema || $skipCache) {
            $columnsResult = $this->source->getFields($tableName, $params);

            $columnsSchema = [];
            foreach ($columnsResult as $column) {
                $columnsSchema[] = $this->createFieldFromArray($column);
            }

            $this->data['columns'][$tableName] = $this->addFieldsRelationship($tableName, $columnsSchema);
        }

        return $columnsSchema;
    }

    public function getFieldsName($tableName)
    {
        $columns = $this->getFields($tableName);

        $columnNames = [];
        foreach ($columns as $column) {
            $columnNames[] = $column->getName();
        }

        return $columnNames;
    }

    /**
     * Get all the columns
     *
     * @param array $params
     *
     * @return Field[]
     */
    public function getAllFields(array $params = [])
    {
        $allColumns = $this->source->getAllFields($params);

        $columns = [];
        foreach($allColumns as $column) {
            $columns[] = $this->createFieldFromArray($column);
        }

        return $columns;
    }

    /**
     * Get a list of columns table grouped by table name
     *
     * @return array
     */
    public function getAllFieldsByCollection()
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
        $collection = $this->getCollection($tableName);
        if ($collection) {
            return $collection->getPrimaryKeyName();
        }

        return false;
    }

    public function hasSystemDateField($tableName)
    {
        $tableObject = $this->getCollection($tableName);

        return $tableObject->getDateCreatedField() || $tableObject->getDateModifiedField();
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
        return DataTypes::isNumericType($type);
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
        return DataTypes::isStringType($type);
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
        return DataTypes::isIntegerType($type);
    }

    /**
     * Checks whether the given type is decimal type
     *
     * @param $type
     *
     * @return bool
     */
    public function isFloatingPointType($type)
    {
        return static::isFloatingPointType($type);
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
            \Directus\base_path(),
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
                'name' => \Directus\uc_convert($key)
            ];
        }

        return $templatesData;
    }

    /**
     * Returns all directus system collections name
     *
     * @return array
     */
    public static function getSystemCollections()
    {
        return [
            static::COLLECTION_ACTIVITY,
            static::COLLECTION_ACTIVITY_SEEN,
            static::COLLECTION_COLLECTIONS,
            static::COLLECTION_COLLECTION_PRESETS,
            static::COLLECTION_FIELDS,
            static::COLLECTION_FILES,
            static::COLLECTION_FOLDERS,
            static::COLLECTION_MIGRATIONS,
            static::COLLECTION_ROLES,
            static::COLLECTION_PERMISSIONS,
            static::COLLECTION_RELATIONS,
            static::COLLECTION_REVISIONS,
            static::COLLECTION_SETTINGS,
            static::COLLECTION_USER_ROLES,
            static::COLLECTION_USERS
        ];
    }

    /**
     * Gets a collection object from an array attributes data
     * @param $data
     *
     * @return Collection
     */
    public function createCollectionFromArray($data)
    {
        return new Collection($data);
    }

    /**
     * Creates a column object from the given array
     *
     * @param array $column
     *
     * @return Field
     */
    public function createFieldFromArray($column)
    {
        // PRIMARY KEY must be required
        if ($column['primary_key']) {
            $column['required'] = true;
        }

        $options = json_decode(isset($column['options']) ? $column['options'] : '', true);
        $column['options'] = $options ? $options : null;

        $type = strtolower($column['type']);
        // NOTE: Alias column must are nullable
        if (DataTypes::isAliasType($type)) {
            $column['nullable'] = true;
        }

        if (DataTypes::isFloatingPointType($type)) {
            $column['length'] = sprintf('%d,%d', $column['precision'], $column['scale']);
        } else if (DataTypes::isListType($type)) {
            $column['length'] = implode(',', array_map(function ($value) {
                return sprintf('"%s"', $value);
            }, $column['length']));
        } else if (DataTypes::isIntegerType($type)) {
            $column['length'] = $column['precision'];
        } else {
            $column['length'] = $column['char_length'];
        }

        // NOTE: MariaDB store "NULL" as a string on some data types such as VARCHAR.
        // We reserved the word "NULL" on nullable data type to be actually null
        if ($column['nullable'] === true && $column['default_value'] == 'NULL') {
            $column['default_value'] = null;
        }

        return new Field($column);
    }

    /**
     * @param string $collectionName
     * @param Field[] $fields
     *
     * @return array|Field[]
     */
    public function addFieldsRelationship($collectionName, array $fields)
    {
        $fieldsRelation = $this->getRelationshipsData($collectionName);

        foreach ($fields as $field) {
            if (array_key_exists($field->getName(), $fieldsRelation)) {
                $this->addFieldRelationship($field, $fieldsRelation[$field->getName()]);
            }
        }

        return $fields;
    }

    /**
     * @param Field $field
     * @param $relationshipData
     */
    protected function addFieldRelationship(Field $field, $relationshipData)
    {
        // Set all FILE data type related to directus files (M2O)
        if (DataTypes::isFilesType($field->getType())) {
            $field->setRelationship([
                'collection_a' => $field->getCollectionName(),
                'field_a' => $field->getName(),
                'collection_b' => static::COLLECTION_FILES,
                'field_b' => 'id'
            ]);
        } else {
            $field->setRelationship($relationshipData);
        }
    }

    /**
     * @param string $collectionName
     *
     * @return array
     */
    protected function getRelationshipsData($collectionName)
    {
        $relationsResult = $this->source->getRelations($collectionName);
        $fieldsRelation = [];

        foreach ($relationsResult as $relation) {
            $isJunctionCollection = ArrayUtils::get($relation, 'junction_collection') === $collectionName;

            if ($isJunctionCollection) {
                if (!isset($relation['junction_key_a']) || !isset($relation['junction_key_b'])) {
                    continue;
                }

                $junctionKeyA = $relation['junction_key_a'];
                $fieldsRelation[$junctionKeyA] = [
                    'collection_a' => $relation['junction_collection'],
                    'field_a' => $junctionKeyA,
                    'junction_key_a' => null,
                    'junction_collection' => null,
                    'junction_key_b' => null,
                    'collection_b' => ArrayUtils::get($relation, 'collection_a'),
                    'field_b' => null,
                ];

                $junctionKeyB = $relation['junction_key_b'];
                $fieldsRelation[$junctionKeyB] = [
                    'collection_a' => $relation['junction_collection'],
                    'field_a' => $junctionKeyB,
                    'junction_key_a' => null,
                    'junction_collection' => null,
                    'junction_key_b' => null,
                    'collection_b' => ArrayUtils::get($relation, 'collection_b'),
                    'field_b' => null,
                ];
            } else {
                $fieldsRelation[$relation['field_a']] = $relation;
            }
        }

        return $fieldsRelation;
    }

    /**
     * Checks whether the given type is a unique type
     *
     * @param $type
     *
     * @return bool
     */
    public function isUniqueFieldType($type)
    {
        return DataTypes::isUniqueType($type);
    }

    protected function addCollection($name, $schema)
    {
        // save the column into the data
        // @NOTE: this is the early implementation of cache
        // soon this will be change to cache
        $this->data['tables'][$name] = $schema;
    }

    protected function addField(Field $column)
    {
        $tableName = $column->getCollectionName();
        $columnName = $column->getName();
        $this->data['fields'][$tableName][$columnName] = $column;
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
     * Gets the column type based the schema adapter
     *
     * @param string $type
     *
     * @return string
     */
    public function getDataType($type)
    {
        return $this->source->getDataType($type);
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
