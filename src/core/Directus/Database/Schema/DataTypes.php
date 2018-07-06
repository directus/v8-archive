<?php

namespace Directus\Database\Schema;

final class DataTypes
{
    const TYPE_CHAR         = 'char';
    const TYPE_VARCHAR      = 'varchar';
    const TYPE_TINY_TEXT    = 'tinytext';
    const TYPE_TEXT         = 'text';
    const TYPE_MEDIUM_TEXT  = 'mediumtext';
    const TYPE_LONGTEXT     = 'longtext';
    const TYPE_UUID         = 'uuid';
    const TYPE_ARRAY        = 'array';
    const TYPE_TINY_JSON    = 'tinyjson';
    const TYPE_JSON         = 'json';
    const TYPE_MEDIUM_JSON  = 'mediumjson';
    const TYPE_LONG_JSON    = 'longjson';

    const TYPE_TIME         = 'time';
    const TYPE_DATE         = 'date';
    const TYPE_DATETIME     = 'datetime';
    const TYPE_TIMESTAMP    = 'timestamp';

    const TYPE_TINY_INT     = 'tinyint';
    const TYPE_SMALL_INT    = 'smallint';
    const TYPE_INTEGER      = 'integer';
    const TYPE_INT          = 'int';
    const TYPE_MEDIUM_INT   = 'mediumint';
    const TYPE_BIG_INT      = 'bigint';
    const TYPE_SERIAL       = 'serial';
    const TYPE_FLOAT        = 'float';
    const TYPE_DOUBLE       = 'double';
    const TYPE_DECIMAL      = 'decimal';
    const TYPE_REAL         = 'real';
    const TYPE_NUMERIC      = 'numeric';
    const TYPE_CURRENCY     = 'numeric';
    const TYPE_BIT          = 'bit';
    const TYPE_BOOL         = 'bool';
    const TYPE_BOOLEAN      = 'boolean';

    const TYPE_BINARY       = 'binary';
    const TYPE_VARBINARY    = 'varbinary';
    const TYPE_TINY_BLOB    = 'tinyblob';
    const TYPE_BLOB         = 'blob';
    const TYPE_MEDIUM_BLOB  = 'mediumblob';
    const TYPE_LONG_BLOB    = 'longblob';

    const TYPE_SET          = 'set';
    const TYPE_ENUM         = 'enum';

    const TYPE_ALIAS        = 'alias';
    const TYPE_M2M          = 'm2m';
    const TYPE_O2M          = 'o2m';
    const TYPE_GROUP        = 'group';

    const TYPE_FILE         = 'file';
    const TYPE_TRANSLATION  = 'translation';

    const TYPE_PRIMARY_KEY       = 'primary_key';
    const TYPE_STATUS            = 'status';

    const TYPE_SORT              = 'sort';
    const TYPE_DATETIME_CREATED  = 'datetime_created';
    const TYPE_DATETIME_MODIFIED = 'datetime_modified';
    const TYPE_USER_CREATED      = 'user_created';
    const TYPE_USER_MODIFIED     = 'user_modified';

    /**
     * Returns a list all data types
     *
     * @return array
     */
    public static function getAllTypes()
    {
        return array_unique(array_merge(
            static::getStringTypes(),
            static::getJSONTypes(),
            static::getNumericTypes(),
            static::getDateTimeTypes(),
            static::getBooleanTypes(),
            static::getListTypes(),
            static::getBinaryTypes(),
            static::getUniqueTypes(),
            static::getFilesType(),
            static::getAliasTypes()
        ));
    }

    /**
     * Returns a list of String data types
     *
     * @return array
     */
    public static function getStringTypes()
    {
        return array_merge([
            static::TYPE_CHAR,
            static::TYPE_VARCHAR,
            static::TYPE_TINY_TEXT,
            static::TYPE_TEXT,
            static::TYPE_MEDIUM_TEXT,
            static::TYPE_LONGTEXT,
            static::TYPE_UUID,
            static::TYPE_ARRAY
        ], static::getJSONTypes(), static::getListTypes());
    }

    /**
     * Checks whether the given type is a string type or not
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isStringType($type)
    {
        return in_array(strtolower($type), static::getStringTypes());
    }

    /**
     * Returns a list of JSON data types
     *
     * @return array
     */
    public static function getJSONTypes()
    {
        return [
            static::TYPE_TINY_JSON,
            static::TYPE_JSON,
            static::TYPE_MEDIUM_JSON,
            static::TYPE_LONG_JSON
        ];
    }

    /**
     * Returns a list of Date/Time data types
     *
     * @return array
     */
    public static function getDateTimeTypes()
    {
        return [
            static::TYPE_TIME,
            static::TYPE_DATE,
            static::TYPE_DATETIME
        ];
    }

    /**
     * Returns a list of Numeric data types
     *
     * @return array
     */
    public static function getNumericTypes()
    {
        return array_merge(
            static::getIntegerTypes(),
            static::getFloatingPointTypes()
        );
    }

    /**
     * Checks whether or not the given type is a numeric type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isNumericType($type)
    {
        return in_array(strtolower($type), static::getNumericTypes());
    }

    /**
     * Returns a list of Integer data types
     *
     * @return array
     */
    public static function getIntegerTypes()
    {
        return [
            static::TYPE_TINY_INT,
            static::TYPE_SMALL_INT,
            static::TYPE_INTEGER,
            static::TYPE_INT,
            static::TYPE_MEDIUM_INT,
            static::TYPE_BIG_INT,
            static::TYPE_SERIAL
        ];
    }

    /**
     * Checks whether or not the given type is a integer type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isIntegerType($type)
    {
        return in_array(strtolower($type), static::getIntegerTypes());
    }

    /**
     * Returns a list of floating point numeric types
     *
     * @return array
     */
    public static function getFloatingPointTypes()
    {
        return [
            static::TYPE_FLOAT,
            static::TYPE_DOUBLE,
            static::TYPE_DECIMAL,
            static::TYPE_REAL,
            static::TYPE_NUMERIC,
            static::TYPE_CURRENCY
        ];
    }

    /**
     * Checks whether or not the given type is a floating point number type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isFloatingPointType($type)
    {
        return in_array(strtolower($type), static::getFloatingPointTypes());
    }

    /**
     * Returns a list of Boolean data types
     *
     * @return array
     */
    public static function getBooleanTypes()
    {
        return [
            static::TYPE_BIT,
            static::TYPE_BOOL,
            static::TYPE_BOOLEAN
        ];
    }

    /**
     * Returns a list of "list" (Ex SET/ENUM) data types
     *
     * @return array
     */
    public static function getListTypes()
    {
        return [
            static::TYPE_SET,
            static::TYPE_ENUM
        ];
    }

    /**
     * Checks if the given type is a list type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isListType($type)
    {
        return in_array(strtolower($type), static::getListTypes());
    }

    /**
     * Returns a list of Binary data types
     *
     * @return array
     */
    public static function getBinaryTypes()
    {
        return [
            static::TYPE_BINARY,
            static::TYPE_VARBINARY,
            static::TYPE_TINY_BLOB,
            static::TYPE_BLOB,
            static::TYPE_MEDIUM_BLOB,
            static::TYPE_LONG_BLOB
        ];
    }

    /**
     * Returns all the alias data types
     *
     * @return array
     */
    public static function getAliasTypes()
    {
        return [
            static::TYPE_ALIAS,
            static::TYPE_M2M,
            static::TYPE_O2M,
            static::TYPE_GROUP,
            static::TYPE_TRANSLATION
        ];
    }

    /**
     * Checks whether the given type is an alias type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isAliasType($type)
    {
        return in_array(strtolower($type), static::getAliasTypes());
    }

    /**
     * Returns all the files type
     *
     * @return array
     */
    public static function getFilesType()
    {
        return [
            static::TYPE_FILE
        ];
    }

    /**
     * Checks whether or not the given type is file type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isFilesType($type)
    {
        return in_array(strtolower($type), static::getFilesType());
    }

    /**
     * Returns all the date types
     *
     * @return array
     */
    public static function getSystemDateTypes()
    {
        return [
            static::TYPE_DATETIME_CREATED,
            static::TYPE_DATETIME_MODIFIED
        ];
    }

    /**
     * Checks whether or not the given type is system date type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isSystemDateType($type)
    {
        return in_array(strtolower($type), static::getSystemDateTypes());
    }

    /**
     * Returns all the unique data types
     *
     * Only one of these types can exists per collection
     *
     * @return array
     */
    public static function getUniqueTypes()
    {
        return array_merge([
            static::TYPE_PRIMARY_KEY,
            static::TYPE_USER_CREATED,
            static::TYPE_USER_MODIFIED,
            static::TYPE_STATUS,
            static::TYPE_SORT
        ], static::getSystemDateTypes());
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isUniqueType($type)
    {
        return in_array(strtolower($type), static::getUniqueTypes());
    }

    /**
     * Returns all the type that allows different data types
     *
     * @return array
     */
    public static function getMultiDataTypeTypes()
    {
        return [
            static::TYPE_PRIMARY_KEY,
            static::TYPE_STATUS,
        ];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function isMultiDataTypeType($type)
    {
        return in_array(strtolower($type), static::getMultiDataTypeTypes());
    }

    /**
     * Returns a list of types that requires a length
     *
     * @return array
     */
    public static function getLengthTypes()
    {
        return array_merge(
            [
                static::TYPE_CHAR,
                static::TYPE_VARCHAR,
                static::TYPE_UUID,
                static::TYPE_ARRAY
            ],
            static::getListTypes(),
            static::getNumericTypes(),
            static::getListTypes()
        );
    }

    /**
     * Checks whether a type requires a length
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isLengthType($type)
    {
        return in_array(strtolower($type), static::getLengthTypes());
    }
}
