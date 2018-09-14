<?php

namespace Directus\Database\Schema;

final class DataTypes
{
    const TYPE_ALIAS                = 'alias';
    const TYPE_ARRAY                = 'array';
    const TYPE_BOOLEAN              = 'boolean';
    const TYPE_DATETIME             = 'datetime';
    const TYPE_FILE                 = 'file';
    const TYPE_GROUP                = 'group';
    const TYPE_NUMBER               = 'number';
    const TYPE_JSON                 = 'json';
    const TYPE_LANG                 = 'lang';
    const TYPE_M2O                  = 'm2o';
    const TYPE_O2M                  = 'o2m';
    const TYPE_SORT                 = 'sort';
    const TYPE_STATUS               = 'status';
    const TYPE_STRING               = 'string';
    const TYPE_TRANSLATION          = 'translation';
    const TYPE_UUID                 = 'uuid';
    const TYPE_DATETIME_CREATED     = 'datetime_created';
    const TYPE_DATETIME_MODIFIED    = 'datetime_modified';
    const TYPE_USER_CREATED         = 'user_created';
    const TYPE_USER_MODIFIED        = 'user_modified';

    /**
     * Returns a list all data types
     *
     * @return array
     */
    public static function getAllTypes()
    {
        return [
            static::TYPE_ALIAS,
            static::TYPE_ARRAY,
            static::TYPE_BOOLEAN,
            static::TYPE_DATETIME,
            static::TYPE_FILE,
            static::TYPE_GROUP,
            static::TYPE_NUMBER,
            static::TYPE_JSON,
            static::TYPE_LANG,
            static::TYPE_M2O,
            static::TYPE_O2M,
            static::TYPE_SORT,
            static::TYPE_STATUS,
            static::TYPE_STRING,
            static::TYPE_TRANSLATION,
            static::TYPE_UUID,
            static::TYPE_DATETIME_CREATED,
            static::TYPE_DATETIME_MODIFIED,
            static::TYPE_USER_CREATED,
            static::TYPE_USER_MODIFIED,
        ];
    }

    /**
     * Checks whether or not the given type is a array type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isArray($type)
    {
        return strtolower($type) === static::TYPE_ARRAY;
    }

    /**
     * Checks whether or not the given type is a boolean type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isBoolean($type)
    {
        return strtolower($type) === static::TYPE_BOOLEAN;
    }

    /**
     * Checks whether or not the given type is a json type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isJson($type)
    {
        return strtolower($type) === static::TYPE_JSON;
    }

    /**
     * Checks whether or not the given type is a string type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isStringType($type)
    {
        return strtolower($type) === static::TYPE_STRING;
    }

    /**
     * Returns a list of Date/Time data types
     *
     * @return array
     */
    public static function getDateTimeTypes()
    {
        return array_merge(static::getSystemDateTypes(), [
            static::TYPE_DATETIME,
        ]);
    }

    /**
     * Checks whether or not the given type is a date time type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isDateTimeType($type)
    {
        return in_array(strtolower($type), static::getDateTimeTypes());
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
        return strtolower($type) === $type;
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
     * Returns all the o2m data types
     *
     * @return array
     */
    public static function getO2MTypes()
    {
        return [
            static::TYPE_O2M,
            static::TYPE_TRANSLATION
        ];
    }

    /**
     * Checks whether the given type is an o2m type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isO2MType($type)
    {
        return in_array(strtolower($type), static::getO2MTypes());
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
            static::TYPE_USER_CREATED,
            static::TYPE_USER_MODIFIED,
            static::TYPE_STATUS,
            static::TYPE_SORT,
            static::TYPE_LANG,
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
     * Returns a list of types that requires a length
     *
     * @return array
     */
    public static function getLengthTypes()
    {
        return [
            static::TYPE_NUMBER,
            static::TYPE_STRING,
            static::TYPE_ARRAY,
            static::TYPE_ARRAY,
            static::TYPE_LANG
        ];
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

    /**
     * Checks whether the given type is translations type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isTranslationsType($type)
    {
        return static::equals(static::TYPE_TRANSLATION, $type);
    }

    /**
     * Checks whether the given type is lang type
     *
     * @param string $type
     *
     * @return bool
     */
    public static function isLangType($type)
    {
        return static::equals(static::TYPE_LANG, $type);
    }

    /**
     * Checks whether or not a given type exists
     *
     * @param string $type
     *
     * @return bool
     */
    public static function exists($type)
    {
        return in_array(strtolower($type), static::getAllTypes());
    }

    /**
     * Compare if two types are equal
     *
     * @param string $typeA
     * @param string $typeB
     *
     * @return bool
     */
    public static function equals($typeA, $typeB)
    {
        return strtolower($typeA) === strtolower($typeB);
    }
}
