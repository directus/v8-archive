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
    const TYPE_CSV          = 'csv';
    const TYPE_UUID         = 'uuid';
    const TYPE_ARRAY        = 'array';
    const TYPE_TINY_JSON    = 'tinyjson';
    const TYPE_JSON         = 'json';
    const TYPE_MEDIUM_JSON  = 'mediumjson';
    const TYPE_LONG_JSON    = 'longjson';

    const TYPE_TIME         = 'time';
    const TYPE_DATE         = 'date';
    const TYPE_DATETIME     = 'datetime';

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

    /**
     * Returns a list all data types
     *
     * @return array
     */
    public static function getAllTypes()
    {
        return array_merge(
            static::getStringTypes(),
            static::getJSONTypes(),
            static::getNumericTypes(),
            static::getDateTimeTypes(),
            static::getBooleanTypes(),
            static::getListTypes(),
            static::getBinaryTypes()
        );
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
            static::TYPE_CSV,
            static::TYPE_UUID,
            static::TYPE_ARRAY
        ], static::getJSONTypes(), static::getListTypes());
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
}
