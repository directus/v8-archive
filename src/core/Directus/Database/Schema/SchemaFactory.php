<?php

namespace Directus\Database\Schema;

use Directus\Database\Ddl\Column\Bit;
use Directus\Database\Ddl\Column\CollectionLength;
use Directus\Database\Ddl\Column\Custom;
use Directus\Database\Ddl\Column\Double;
use Directus\Database\Ddl\Column\Enum;
use Directus\Database\Ddl\Column\LongBlob;
use Directus\Database\Ddl\Column\LongText;
use Directus\Database\Ddl\Column\MediumBlob;
use Directus\Database\Ddl\Column\MediumInteger;
use Directus\Database\Ddl\Column\MediumText;
use Directus\Database\Ddl\Column\Numeric;
use Directus\Database\Ddl\Column\Real;
use Directus\Database\Ddl\Column\Set;
use Directus\Database\Ddl\Column\SmallInteger;
use Directus\Database\Ddl\Column\TinyBlob;
use Directus\Database\Ddl\Column\TinyInteger;
use Directus\Database\Ddl\Column\TinyText;
use Directus\Database\Exception\FieldAlreadyHasUniqueKeyException;
use Directus\Database\Exception\UnknownTypeException;
use Directus\Util\ArrayUtils;
use Directus\Validator\Exception\InvalidRequestException;
use Directus\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;
use Zend\Db\Sql\AbstractSql;
use Zend\Db\Sql\Ddl\AlterTable;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;
use Zend\Db\Sql\Ddl\Constraint\UniqueKey;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl\Column\AbstractLengthColumn;
use Zend\Db\Sql\Ddl\Column\AbstractPrecisionColumn;

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
        $dataType = isset($data['datatype']) ? $data['datatype'] : $type;
        $autoincrement = ArrayUtils::get($data, 'auto_increment', false);
        $unique = ArrayUtils::get($data, 'unique', false);
        $primaryKey = ArrayUtils::get($data, 'primary_key', false);
        $length = ArrayUtils::get($data, 'length', $this->schemaManager->getFieldDefaultLength($type));
        $nullable = ArrayUtils::get($data, 'nullable', true);
        $default = ArrayUtils::get($data, 'default_value', null);
        $unsigned = !ArrayUtils::get($data, 'signed', false);
        $note = ArrayUtils::get($data, 'note');
        // ZendDB doesn't support encoding nor collation

        $column = $this->schemaManager->getSource()->createColumnFromDataType($name, $dataType);
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
        if ($column instanceof \Zend\Db\Sql\Ddl\Column\Integer) {
            $column->setOption('identity', $autoincrement);
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

        //WORKAROUND ZendDB doesn't know anything about PostgreSQL for instance, we might need to override some of the table specifications depending on the RDBMS used
        //We monkey-path Zend-DB behaviour here and not through its own source repository with a decorator
        $sqlFixed = $this->schemaManager->getSource()->buildSql($table, $sql);
        // TODO: Allow charset and comment
        return $connection->query(
            $sqlFixed,
            $connection::QUERY_MODE_EXECUTE
        );
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
