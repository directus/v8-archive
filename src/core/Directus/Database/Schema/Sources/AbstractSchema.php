<?php

namespace Directus\Database\Schema\Sources;

use Directus\Database\Schema\DataTypes;
use Directus\Database\Schema\Object\Field;
use Directus\Util\ArrayUtils;
use function Directus\is_valid_datetime;

abstract class AbstractSchema implements SchemaInterface
{
    /**
     * @var string
     */
    protected $datetimeFormat = 'Y-m-d H:i:s';

    /**
     * Cast records values by its column data type
     *
     * @param array    $records
     * @param Field[] $fields
     *
     * @return array
     */
    public function castRecordValues(array $records, $fields)
    {
        // hotfix: records sometimes are no set as an array of rows.
        $singleRecord = false;
        if (!ArrayUtils::isNumericKeys($records)) {
            $records = [$records];
            $singleRecord = true;
        }

        foreach ($fields as $field) {
            foreach ($records as $index => $record) {
                $fieldName = $field->getName();
                if (ArrayUtils::has($record, $fieldName)) {
                    $type = $field->getDataType();

                    $records[$index][$fieldName] = $this->castValue($record[$fieldName], $type);
                }
            }
        }

        return $singleRecord ? reset($records) : $records;
    }

    /**
     * Parse records value by its column data type
     *
     * @see AbastractSchema::castRecordValues
     *
     * @param array $records
     * @param $columns
     *
     * @return array
     */
    public function parseRecordValuesByType(array $records, $columns)
    {
        return $this->castRecordValues($records, $columns);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultLengths()
    {
        return [
            // 'ALIAS' => static::INTERFACE_ALIAS,
            // 'MANYTOMANY' => static::INTERFACE_ALIAS,
            // 'ONETOMANY' => static::INTERFACE_ALIAS,

            // 'BIT' => static::INTERFACE_TOGGLE,
            // 'TINYINT' => static::INTERFACE_TOGGLE,

            // 'MEDIUMBLOB' => static::INTERFACE_BLOB,
            // 'BLOB' => static::INTERFACE_BLOB,

            // 'TINYTEXT' => static::INTERFACE_TEXT_AREA,
            // 'TEXT' => static::INTERFACE_TEXT_AREA,
            // 'MEDIUMTEXT' => static::INTERFACE_TEXT_AREA,
            // 'LONGTEXT' => static::INTERFACE_TEXT_AREA,

            'CHAR' => 1,
            'VARCHAR' => 255,
            // 'POINT' => static::INTERFACE_TEXT_INPUT,

            // 'DATETIME' => static::INTERFACE_DATETIME,
            // 'TIMESTAMP' => static::INTERFACE_DATETIME,

            // 'DATE' => static::INTERFACE_DATE,

            // 'TIME' => static::INTERFACE_TIME,

            // 'YEAR' => static::INTERFACE_NUMERIC,
            // 'SMALLINT' => static::INTERFACE_NUMERIC,
            // 'MEDIUMINT' => static::INTERFACE_NUMERIC,
            'INT' => 11,
            'INTEGER' => 11,
            // 'BIGINT' => static::INTERFACE_NUMERIC,
            // 'FLOAT' => static::INTERFACE_NUMERIC,
            // 'DOUBLE' => static::INTERFACE_NUMERIC,
            // 'DECIMAL' => static::INTERFACE_NUMERIC,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getColumnDefaultLength($type)
    {
        return ArrayUtils::get($this->getDefaultLengths(), strtoupper($type), null);
    }

    /**
     * @inheritdoc
     */
    public function isType($type, array $list)
    {
        return in_array(strtolower($type), $list);
    }

    /**
     * @inheritdoc
     */
    public function getTypeFromSource($sourceType)
    {
        $type = null;

        switch (strtolower($sourceType)) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int': // alias: integer
            case 'year':
                $type = DataTypes::TYPE_INTEGER;
                break;
            case 'decimal': // alias: dec, fixed
            case 'numeric':
            case 'float': // alias: real
            case 'double': // alias: double precision, real
                $type = DataTypes::TYPE_DECIMAL;
                break;
            case 'bit':
            case 'binary':
            case 'varbinary':
                $type = DataTypes::TYPE_BINARY;
                break;
            case 'datetime':
            case 'timestamp':
                $type = DataTypes::TYPE_DATETIME;
                break;
            case 'date':
                $type = DataTypes::TYPE_DATE;
                break;
            case 'time':
                $type = DataTypes::TYPE_TIME;
                break;
            case 'char':
            case 'varchar':
            case 'enum':
            case 'set':
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                $type = DataTypes::TYPE_STRING;
                break;
            case 'json':
                $type = DataTypes::TYPE_JSON;
                break;
        }

        return $type;
    }

    /**
     * @inheritdoc
     */
    public function getDateTimeFormat()
    {
        return $this->datetimeFormat;
    }

    /**
     * Does the RDBMS handle sequences?
     * 
     * @return boolean
     */
    public function hasSequences()
    {
        return false;
    }

    /**
     * Get the last generated value for this field, if it's based on a sequence or auto-incremented
     * Do not use along with SequenceFeature
     * 
     * @return boolean
     */
    public function getLastGeneratedId($abstractTableGateway, $table, $field)
    {
        return (int)$abstractTableGateway->getLastInsertValue();
    }
    public function getNextGeneratedId($abstractTableGateway, $table, $field)
    {
        //Not handled
        return null;
    }

    /**
     * Fix defaultZendDB choices if applicable
     *
     * @param AbstractSql|AlterTable|CreateTable $table
     * @param Sql $sql
     *
     * @return String
     */
    public function buildSql($table, $sql)
    {
        //Do nothing
        return $sql->buildSqlString($table);
    }

    /**
     * Transform if needed the default value of a field
     * @param array $field
     * 
     * @return array $field
     */
    public function transformField(array $field)
    {
        $transformedField = $field;
        // NOTE: MariaDB store "NULL" as a string on some data types such as VARCHAR.
        // We reserved the word "NULL" on nullable data type to be actually null
        if ($field['nullable'] === true && $field['default_value'] == 'NULL') {
            $transformedField['default_value'] = null;
        }
        if (DataTypes::isDateTimeType($field['type']) && is_valid_datetime($field['default_value'], $this->getDateTimeFormat())) {
            $transformedField['default_value'] = DateTimeUtils::createDateFromFormat($this->getDateTimeFormat(), $field['default_value'])->toISO8601Format();
        }
        return $transformedField;
    }
}
