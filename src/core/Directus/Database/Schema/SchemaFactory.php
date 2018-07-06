<?php

namespace Directus\Database\Schema;

use Directus\Database\Ddl\Column\Bit;
use Directus\Database\Ddl\Column\Boolean;
use Directus\Database\Ddl\Column\CollectionLength;
use Directus\Database\Ddl\Column\Double;
use Directus\Database\Ddl\Column\Enum;
use Directus\Database\Ddl\Column\File;
use Directus\Database\Ddl\Column\LongBlob;
use Directus\Database\Ddl\Column\LongText;
use Directus\Database\Ddl\Column\MediumBlob;
use Directus\Database\Ddl\Column\MediumInteger;
use Directus\Database\Ddl\Column\MediumText;
use Directus\Database\Ddl\Column\Numeric;
use Directus\Database\Ddl\Column\Real;
use Directus\Database\Ddl\Column\Serial;
use Directus\Database\Ddl\Column\Set;
use Directus\Database\Ddl\Column\SmallInteger;
use Directus\Database\Ddl\Column\TinyBlob;
use Directus\Database\Ddl\Column\TinyInteger;
use Directus\Database\Ddl\Column\TinyText;
use Directus\Database\Ddl\Column\Uuid;
use Directus\Database\Exception\FieldAlreadyHasUniqueKeyException;
use Directus\Database\Exception\UnknownDataTypeException;
use Directus\Exception\Exception;
use Directus\Util\ArrayUtils;
use Directus\Validator\Exception\InvalidRequestException;
use Directus\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Ddl\AlterTable;
use Zend\Db\Sql\Ddl\Column\AbstractLengthColumn;
use Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn;
use Zend\Db\Sql\Ddl\Column\BigInteger;
use Zend\Db\Sql\Ddl\Column\Binary;
use Zend\Db\Sql\Ddl\Column\Blob;
use Zend\Db\Sql\Ddl\Column\Char;
use Zend\Db\Sql\Ddl\Column\Column;
use Zend\Db\Sql\Ddl\Column\Date;
use Zend\Db\Sql\Ddl\Column\Datetime;
use Zend\Db\Sql\Ddl\Column\Decimal;
use Zend\Db\Sql\Ddl\Column\Floating;
use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Column\Text;
use Zend\Db\Sql\Ddl\Column\Time;
use Zend\Db\Sql\Ddl\Column\Timestamp;
use Zend\Db\Sql\Ddl\Column\Varbinary;
use Zend\Db\Sql\Ddl\Column\Varchar;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Sql;

class SchemaFactory
{
    /**
     * @var SchemaManager
     */
    protected $schemaManager;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(SchemaManager $manager)
    {
        $this->schemaManager = $manager;
        $this->validator = new Validator();
    }

    /**
     * Create a new table
     *
     * @param string $name
     * @param array $columnsData
     *
     * @return CreateTable
     */
    public function createTable($name, array $columnsData = [])
    {
        $table = new CreateTable($name);
        $columns = $this->createColumns($columnsData);

        foreach ($columnsData as $column) {
            if (ArrayUtils::get($column, 'primary_key', false)) {
                $table->addConstraint(new PrimaryKey($column['field']));
            } else if (ArrayUtils::get($column, 'unique') == true) {
                $table->addConstraint(new UniqueKey($column['field']));
            }
        }

        foreach ($columns as $column) {
            $table->addColumn($column);
        }

        return $table;
    }

    /**
     * Alter an existing table
     *
     * @param $name
     * @param array $data
     *
     * @return AlterTable
     *
     * @throws FieldAlreadyHasUniqueKeyException
     */
    public function alterTable($name, array $data)
    {
        $table = new AlterTable($name);

        $toAddColumnsData = ArrayUtils::get($data, 'add', []);
        $toAddColumns = $this->createColumns($toAddColumnsData);
        foreach ($toAddColumns as $column) {
            $table->addColumn($column);

            $options = $column->getOptions();
            if (!is_array($options)) {
                continue;
            }

            if (ArrayUtils::get($options, 'primary_key') == true) {
                $table->addConstraint(new PrimaryKey($column->getName()));
            } if (ArrayUtils::get($options, 'unique') == true) {
                $table->addConstraint(new UniqueKey($column->getName()));
            }
        }

        $toChangeColumnsData = ArrayUtils::get($data, 'change', []);
        $toChangeColumns = $this->createColumns($toChangeColumnsData);
        foreach ($toChangeColumns as $column) {
            $table->changeColumn($column->getName(), $column);

            $options = $column->getOptions();
            if (!is_array($options)) {
                continue;
            }

            if (ArrayUtils::get($options, 'primary_key') == true) {
                $table->addConstraint(new PrimaryKey($column->getName()));
            } if (ArrayUtils::get($options, 'unique') == true) {
                $table->addConstraint(new UniqueKey($column->getName()));
            }
        }

        $toDropColumnsName = ArrayUtils::get($data, 'drop', []);
        foreach ($toDropColumnsName as $column) {
            $table->dropColumn($column);
        }

        return $table;
    }

    /**
     * @param array $data
     *
     * @return Column[]
     */
    public function createColumns(array $data)
    {
        $columns = [];
        foreach ($data as $column) {
            if (!DataTypes::isAliasType(ArrayUtils::get($column, 'type'))) {
                $columns[] = $this->createColumn(ArrayUtils::get($column, 'field'), $column);
            }
        }

        return $columns;
    }

    /**
     * @param string $name
     * @param array $data
     *
     * @return Column
     */
    public function createColumn($name, array $data)
    {
        $this->validate($data);
        $type = ArrayUtils::get($data, 'type');
        $dataType = ArrayUtils::get($data, 'datatype', $type);
        $autoincrement = ArrayUtils::get($data, 'auto_increment', false);
        $unique = ArrayUtils::get($data, 'unique', false);
        $primaryKey = ArrayUtils::get($data, 'primary_key', false);
        $length = ArrayUtils::get($data, 'length', $this->schemaManager->getFieldDefaultLength($type));
        $nullable = ArrayUtils::get($data, 'nullable', true);
        $default = ArrayUtils::get($data, 'default_value', null);
        $unsigned = !ArrayUtils::get($data, 'signed', false);
        $note = ArrayUtils::get($data, 'note');
        // ZendDB doesn't support encoding nor collation

        $column = $this->createColumnFromType($name, $dataType);
        $column->setNullable($nullable);
        $column->setDefault($default);
        $column->setOption('comment', $note);

        if (!$autoincrement && $unique === true) {
            $column->setOption('unique', $unique);
        }

        if ($primaryKey === true) {
            $column->setOption('primary_key', $primaryKey);
        }

        // CollectionLength are SET or ENUM data type
        if ($column instanceof AbstractPrecisionColumn) {
            $parts = !is_array($length) ? explode(',', $length) : $length;
            $column->setDigits($parts[0]);
            $column->setDecimal(isset($parts[1]) ? $parts[1] : 0);
        } else if ($column instanceof AbstractLengthColumn || $column instanceof CollectionLength) {
            $column->setLength($length);
        } else {
            $column->setOption('length', $length);
        }

        // Only works for integers
        if ($column instanceof Integer) {
            $column->setOption('autoincrement', $autoincrement);
            $column->setOption('unsigned', $unsigned);
        }

        return $column;
    }

    /**
     * Creates the given table
     *
     * @param AbstractSql|AlterTable|CreateTable $table
     *
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     */
    public function buildTable(AbstractSql $table)
    {
        $connection = $this->schemaManager->getSource()->getConnection();
        $sql = new Sql($connection);

        // TODO: Allow charset and comment
        return $connection->query(
            $sql->buildSqlString($table),
            $connection::QUERY_MODE_EXECUTE
        );
    }

    /**
     * Creates column based on type
     *
     * @param $name
     * @param $type
     *
     * @return Column
     *
     * @throws UnknownDataTypeException
     */
    protected function createColumnFromType($name, $type)
    {
        switch (strtolower($type)) {
            case DataTypes::TYPE_CHAR:
                $column = new Char($name);
                break;
            case DataTypes::TYPE_VARCHAR:
                $column = new Varchar($name);
                break;
            case DataTypes::TYPE_TINY_JSON:
            case DataTypes::TYPE_TINY_TEXT:
                $column = new TinyText($name);
                break;
            case DataTypes::TYPE_JSON:
            case DataTypes::TYPE_TEXT:
                $column = new Text($name);
                break;
            case DataTypes::TYPE_MEDIUM_JSON:
            case DataTypes::TYPE_MEDIUM_TEXT:
                $column = new MediumText($name);
                break;
            case DataTypes::TYPE_LONG_JSON:
            case DataTypes::TYPE_LONGTEXT:
                $column = new LongText($name);
                break;
            case DataTypes::TYPE_UUID:
                $column = new Uuid($name);
                break;
            case DataTypes::TYPE_ARRAY:
                $column = new Varchar($name);
                break;

            case DataTypes::TYPE_TIME:
                $column = new Time($name);
                break;
            case DataTypes::TYPE_DATE:
                $column = new Date($name);
                break;
            case DataTypes::TYPE_DATETIME:
                $column = new Datetime($name);
                break;
            case DataTypes::TYPE_TIMESTAMP:
                $column = new Timestamp($name);
                break;

            case DataTypes::TYPE_TINY_INT:
                $column = new TinyInteger($name);
                break;
            case DataTypes::TYPE_SMALL_INT:
                $column = new SmallInteger($name);
                break;
            case DataTypes::TYPE_INTEGER:
            case DataTypes::TYPE_INT:
                $column = new Integer($name);
                break;
            case DataTypes::TYPE_MEDIUM_INT:
                $column = new MediumInteger($name);
                break;
            case DataTypes::TYPE_BIG_INT:
                $column = new BigInteger($name);
                break;
            case DataTypes::TYPE_SERIAL:
                $column = new Serial($name);
                break;
            case DataTypes::TYPE_FLOAT:
                $column = new Floating($name);
                break;
            case DataTypes::TYPE_DOUBLE:
                $column = new Double($name);
                break;
            case DataTypes::TYPE_DECIMAL:
                $column = new Decimal($name);
                break;
            case DataTypes::TYPE_REAL:
                $column = new Real($name);
                break;
            case DataTypes::TYPE_NUMERIC:
            case DataTypes::TYPE_CURRENCY:
                $column = new Numeric($name);
                break;
            case DataTypes::TYPE_BIT:
                $column = new Bit($name);
                break;
            case DataTypes::TYPE_BOOL:
            case DataTypes::TYPE_BOOLEAN:
                $column = new Boolean($name);
                break;

            case DataTypes::TYPE_BINARY:
                $column = new Binary($name);
                break;
            case DataTypes::TYPE_VARBINARY:
                $column = new Varbinary($name);
                break;
            case DataTypes::TYPE_TINY_BLOB:
                $column = new TinyBlob($name);
                break;
            case DataTypes::TYPE_BLOB:
                $column = new Blob($name);
                break;
            case DataTypes::TYPE_MEDIUM_BLOB:
                $column = new MediumBlob($name);
                break;
            case DataTypes::TYPE_LONG_BLOB:
                $column = new LongBlob($name);
                break;

            case DataTypes::TYPE_SET:
                $column = new Set($name);
                break;
            case DataTypes::TYPE_ENUM:
                $column = new Enum($name);
                break;

            case DataTypes::TYPE_FILE:
                $column = new File($name);
                break;

            default:
                throw new UnknownDataTypeException($type);
                break;
        }

        return $column;
    }

    /**
     * @param array $columnData
     *
     * @throws InvalidRequestException
     */
    protected function validate(array $columnData)
    {
        $constraints = [
            'field' => ['required', 'string'],
            'type' => ['required', 'string'],
            'interface' => ['required', 'string']
        ];

        // Copied from route
        // TODO: Route needs a restructure to get the utils code like this shared
        $violations = [];
        $data = ArrayUtils::pick($columnData, array_keys($constraints));
        foreach (array_keys($constraints) as $field) {
            $violations[$field] = $this->validator->validate(ArrayUtils::get($data, $field), $constraints[$field]);
        }

        $messages = [];
        /** @var ConstraintViolationList $violation */
        foreach ($violations as $field => $violation) {
            $iterator = $violation->getIterator();

            $errors = [];
            while ($iterator->valid()) {
                $constraintViolation = $iterator->current();
                $errors[] = $constraintViolation->getMessage();
                $iterator->next();
            }

            if ($errors) {
                $messages[] = sprintf('%s: %s', $field, implode(', ', $errors));
            }
        }

        if (count($messages) > 0) {
            throw new InvalidRequestException(implode(' ', $messages));
        }
    }
}
