<?php

namespace Directus\Database\Schema\Sources;

use Directus\Database\Connection;
use Zend\Db\ResultSet\ResultSet;

interface SchemaInterface
{
    const INTERFACE_ALIAS       = 'alias';
    const INTERFACE_BLOB        = 'blob';
    const INTERFACE_DATE        = 'date';
    const INTERFACE_DATETIME    = 'datetime';
    const INTERFACE_NUMERIC     = 'numeric';
    const INTERFACE_TEXT_AREA   = 'textarea';
    const INTERFACE_TEXT_INPUT  = 'text_input';
    const INTERFACE_TIME        = 'time';
    const INTERFACE_TOGGLE      = 'toggle';

    /**
     * @return Connection
     */
    public function getConnection();
    /**
     * Get the schema name
     *
     * @return string
     */
    public function getSchemaName();

    /**
     * Gets a list of all tables structures.
     *
     * @param array $params
     *
     * @return ResultSet
     */
    public function getCollections(array $params = []);

    /**
     * Check whether a given table name exists
     *
     * @param string|array $collectionName
     *
     * @return bool
     */
    public function collectionExists($collectionName);

    /**
     * Gets the structure of the given table name.
     *
     * @param $collectionName
     *
     * @return ResultSet
     */
    public function getCollection($collectionName);

    /**
     * Gets all columns in the given table name.
     *
     * @param string $tableName
     * @param array $params
     *
     * @return ResultSet
     */
    public function getFields($tableName, $params = null);

    /**
     * Gets all columns in the current schema
     *
     * @return ResultSet
     */
    public function getAllFields();

    /**
     * Checks whether the given table name has a given column name
     *
     * @param string $collectionName
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField($collectionName, $fieldName);

    /**
     * Gets the info of the given column name in the given table name
     *
     * @param string $collectionName
     * @param string $fieldName
     *
     * @return array
     */
    public function getField($collectionName, $fieldName);

    /**
     * Gets all relations
     *
     * @return ResultSet
     */
    public function getAllRelations();

    /**
     * Gets the collection fields relations
     *
     * @param string $collectionName
     *
     * @return ResultSet
     */
    public function getRelations($collectionName);

    /**
     * Checks whether the given table name has primary key column
     *
     * @param $tableName
     *
     * @return bool
     */
    // public function hasPrimaryKey($tableName);

    /**
     * Get the primary key of the given table name.
     *
     * @param $tableName
     *
     * @return array
     */
    // public function getPrimaryKey($tableName);

    /**
     * Gets a list with all the tables and column structure and information.
     *
     * @return array
     */
    // public function getFullSchema();

    /**
     * Gets the given column UI name
     *
     * @param $column
     *
     * @return string
     */
    // public function getColumnUI($column);

    /**
     * Adds a primary key to the given column
     *
     * @param $table
     * @param $column
     *
     * @return bool
     */
    // public function addPrimaryKey($table, $column);

    /**
     * Removes the primary key from the given column
     *
     * @param $table
     * @param $column
     *
     * @return bool
     */
    // public function dropPrimaryKey($table, $column);

    /**
     * Cast record values by the schema type
     *
     * @param array $records
     * @param array $columns
     *
     * @return array
     */
    public function castRecordValues(array $records, $columns);

    /**
     * Cast value to its database type.
     *
     * @param mixed $data
     * @param null  $type
     *
     * @return mixed
     */
    public function castValue($data, $type = null);

    /**
     * Gets the default interface name per type
     *
     * @return array
     */
    public function getDefaultInterfaces();

    /**
     * Gets the column type default interface name
     *
     * @param $type - Column type
     *
     * @return string
     */
    public function getColumnDefaultInterface($type);

    /**
     * Gets the default length value per type
     *
     * @return array
     */
    public function getDefaultLengths();

    /**
     * Gets the column type default length
     *
     * @param $type - Column type
     *
     * @return integer
     */
    public function getColumnDefaultLength($type);

    /**
     * Checks if the given type exists in the list
     *
     * @param $type
     * @param array $list
     *
     * @return bool
     */
    public function isType($type, array $list);

    /**
     * Gets Integer data types
     *
     * @return array
     */
    public function getIntegerTypes();

    /**
     * Checks whether the given type is integer type
     *
     * @param $type
     *
     * @return bool
     */
    public function isIntegerType($type);

    /**
     * Gets Decimal data types
     *
     * @return array
     */
    public function getDecimalTypes();

    /**
     * Checks whether the given type is decimal type
     *
     * @param $type
     *
     * @return bool
     */
    public function isDecimalType($type);

    /**
     * Gets Numeric data types
     *
     * @return mixed
     */
    public function getNumericTypes();

    /**
     * Checks whether the given type is numeric type
     *
     * @param $type
     *
     * @return bool
     */
    public function isNumericType($type);

    /**
     * Gets String data types
     *
     * @return mixed
     */
    public function getStringTypes();

    /**
     * Checks whether the given type is string type
     *
     * @param $type
     *
     * @return bool
     */
    public function isStringType($type);
}
