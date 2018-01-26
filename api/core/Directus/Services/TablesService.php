<?php

namespace Directus\Services;

use Directus\Database\Exception\ColumnAlreadyExistsException;
use Directus\Database\Exception\ColumnNotFoundException;
use Directus\Database\Exception\TableAlreadyExistsException;
use Directus\Database\Exception\TableNotFoundException;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\SchemaFactory;
use Directus\Database\TableSchema;
use Directus\Exception\BadRequestException;
use Directus\Exception\ErrorException;
use Directus\Hook\Emitter;
use Directus\Util\ArrayUtils;
use Directus\Validator\Exception\InvalidRequestException;

class TablesService extends AbstractService
{
    /**
     *
     * @param string $name
     * @param array $data
     *
     * @return BaseRowGateway
     *
     * @throws ErrorException
     * @throws InvalidRequestException
     * @throws TableAlreadyExistsException
     */
    public function createTable($name, array $data = [])
    {
        if ($this->getSchemaManager()->tableExists($name)) {
            throw new TableAlreadyExistsException($name);
        }

        if (!$this->isValidName($name)) {
            throw new InvalidRequestException('Invalid table name');
        }

        $success = $this->createTableSchema($name, $data);
        if (!$success) {
            throw new ErrorException('Error creating the table');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_tables');

        $columns = ArrayUtils::get($data, 'columns');
        $this->addColumnsInfo($name, $columns);

        $item = ArrayUtils::omit($data, 'columns');
        $item['table_name'] = $name;

        return $collectionsTableGateway->updateRecord($item);
    }

    /**
     * Updates a table
     *
     * @param $name
     * @param array $data
     *
     * @return BaseRowGateway
     *
     * @throws ErrorException
     * @throws TableNotFoundException
     */
    public function updateTable($name, array $data)
    {
        if (!$this->getSchemaManager()->tableExists($name)) {
            throw new TableNotFoundException($name);
        }

        $tableObject = $this->getSchemaManager()->getTableSchema($name);
        $columns = ArrayUtils::get($data, 'columns', []);
        foreach ($columns as $i => $column) {
            $columnObject = $tableObject->getColumn($column['name']);
            if ($columnObject) {
                $currentColumnData = $columnObject->toArray();
                $currentColumnData['interface'] = $currentColumnData['ui'];
                $columns[$i] = array_merge($currentColumnData, $columns[$i]);
            }
        }

        $data['columns'] = $columns;
        $success = $this->updateTableSchema($name, $data);
        if (!$success) {
            throw new ErrorException('Error creating the table');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_tables');

        $columns = ArrayUtils::get($data, 'columns', []);
        if (!empty($columns)) {
            $this->addColumnsInfo($name, $columns);
        }

        $item = ArrayUtils::omit($data, 'columns');
        $item['table_name'] = $name;

        return $collectionsTableGateway->updateRecord($item);
    }

    /**
     * Adds a column to an existing table
     *
     * @param $collectionName
     * @param $columnName
     * @param array $data
     *
     * @return BaseRowGateway
     *
     * @throws ColumnAlreadyExistsException
     *
     * @throws TableNotFoundException
     */
    public function addColumn($collectionName, $columnName, array $data)
    {
        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getColumn($columnName);
        if ($columnObject) {
            throw new ColumnAlreadyExistsException($columnName);
        }

        $columnData = array_merge($data, [
            'name' => $columnName
        ]);

        $this->updateTableSchema($collectionName, [
            'columns' => [$columnData]
        ]);

        return $this->addColumnInfo($collectionName, $columnData);
    }

    /**
     * Adds a column to an existing table
     *
     * @param $collectionName
     * @param $columnName
     * @param array $data
     *
     * @return BaseRowGateway
     *
     * @throws ColumnNotFoundException
     * @throws TableNotFoundException
     */
    public function changeColumn($collectionName, $columnName, array $data)
    {
        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getColumn($columnName);
        if (!$columnObject) {
            throw new ColumnNotFoundException($columnName);
        }

        $columnData = array_merge($columnObject->toArray(), $data);
        $this->updateTableSchema($collectionName, [
            'columns' => [$columnData]
        ]);

        return $this->addColumnInfo($collectionName, $columnData);
    }

    public function dropColumn($collectionName, $fieldName)
    {
        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getColumn($fieldName);
        if (!$columnObject) {
            throw new ColumnNotFoundException($fieldName);
        }

        if (count($tableObject->getColumns()) === 1) {
            throw new BadRequestException('Cannot delete the last field');
        }

        if (!$this->dropColumnSchema($collectionName, $fieldName)) {
            throw new ErrorException('Error deleting the field');
        }

        if (!$this->removeColumnInfo($collectionName, $fieldName)) {
            throw new ErrorException('Error deleting the field information');
        }
    }

    /**
     * Add columns information to the fields table
     *
     * @param $collectionName
     * @param array $columns
     *
     * @return BaseRowGateway[]
     */
    public function addColumnsInfo($collectionName, array $columns)
    {
        $resultsSet = [];
        foreach ($columns as $column) {
            $resultsSet[] = $this->addColumnInfo($collectionName, $column);
        }

        return $resultsSet;
    }

    /**
     * Add field information to the field system table
     *
     * @param $collectionName
     * @param array $column
     *
     * @return BaseRowGateway
     */
    public function addColumnInfo($collectionName, array $column)
    {
        // TODO: Let's make this info a string ALL the time at this level
        $options = ArrayUtils::get($column, 'options', []);
        $data = [
            'table_name' => $collectionName,
            'column_name' => $column['name'],
            'data_type' => $column['type'],
            'ui' => $column['interface'],
            'required' => ArrayUtils::get($column, 'required', false),
            'sort' => ArrayUtils::get($column, 'sort', false),
            'comment' => ArrayUtils::get($column, 'comment', false),
            'hidden_input' => ArrayUtils::get($column, 'hidden_input', false),
            'hidden_list' => ArrayUtils::get($column, 'hidden_list', false),
            'options' => is_array($options) ? json_encode($options) : $options
        ];

        $fieldsTableGateway = $this->createTableGateway('directus_columns');
        $row = $fieldsTableGateway->findOneByArray([
            'table_name' => $collectionName,
            'column_name' => $column['name']
        ]);

        if ($row) {
            $data['id'] = $row['id'];
        }

        return $fieldsTableGateway->updateRecord($data);
    }

    /**
     * @param $collectionName
     * @param $fieldName
     *
     * @return int
     */
    public function removeColumnInfo($collectionName, $fieldName)
    {
        $fieldsTableGateway = $this->createTableGateway('directus_columns');

        return $fieldsTableGateway->delete([
            'table_name' => $collectionName,
            'column_name' => $fieldName
        ]);
    }

    /**
     * @param $collectionName
     * @param $fieldName
     *
     * @return bool
     */
    protected function dropColumnSchema($collectionName, $fieldName)
    {
        /** @var SchemaFactory $schemaFactory */
        $schemaFactory = $this->container->get('schema_factory');
        $table = $schemaFactory->alterTable($collectionName, [
            'drop' => [
                $fieldName
            ]
        ]);

        return $schemaFactory->buildTable($table) ? true : false;
    }

    /**
     * Drops the given table and its table and columns information
     *
     * @param $name
     *
     * @return bool
     *
     * @throws TableNotFoundException
     */
    public function dropTable($name)
    {
        if (!$this->getSchemaManager()->tableExists($name)) {
            throw new TableNotFoundException($name);
        }

        $tableGateway = $this->createTableGateway($name);

        return $tableGateway->drop();
    }

    /**
     * Checks whether the given name is a valid clean table name
     *
     * @param $name
     *
     * @return bool
     */
    public function isValidName($name)
    {
        $isTableNameAlphanumeric = preg_match("/[a-z0-9]+/i", $name);
        $zeroOrMoreUnderscoresDashes = preg_match("/[_-]*/i", $name);

        return $isTableNameAlphanumeric && $zeroOrMoreUnderscoresDashes;
    }

    /**
     * Gets the table object representation
     *
     * @param $tableName
     *
     * @return \Directus\Database\Object\Table
     */
    public function getTableObject($tableName)
    {
        return TableSchema::getTableSchema($tableName);
    }

    /**
     * @param string $name
     * @param array $data
     *
     * @return bool
     */
    protected function createTableSchema($name, array $data)
    {
        /** @var SchemaFactory $schemaFactory */
        $schemaFactory = $this->container->get('schema_factory');

        $columns = ArrayUtils::get($data, 'columns', []);
        $this->validateSystemFields($columns);
        $table = $schemaFactory->createTable($name, $columns);

        /** @var Emitter $hookEmitter */
        $hookEmitter = $this->container->get('hook_emitter');
        $hookEmitter->run('table.create:before', $name);

        $result = $schemaFactory->buildTable($table);

        $hookEmitter->run('table.create', $name);
        $hookEmitter->run('table.create:after', $name);

        return $result ? true : false;
    }

    /**
     * @param $name
     * @param array $data
     *
     * @return bool
     */
    protected function updateTableSchema($name, array $data)
    {
        /** @var SchemaFactory $schemaFactory */
        $schemaFactory = $this->container->get('schema_factory');

        $columns = ArrayUtils::get($data, 'columns', []);
        $this->validateSystemFields($columns);

        $toAdd = $toChange = [];
        $tableObject = $this->getSchemaManager()->getTableSchema($name);
        foreach ($columns as $i => $column) {
            $columnObject = $tableObject->getColumn($column['name']);
            if ($columnObject) {
                $toChange[] = array_merge($columnObject->toArray(), $column);
            } else {
                $toAdd[] = $column;
            }
        }

        $table = $schemaFactory->alterTable($name, [
            'add' => $toAdd,
            'change' => $toChange
        ]);

        /** @var Emitter $hookEmitter */
        $hookEmitter = $this->container->get('hook_emitter');
        $hookEmitter->run('table.update:before', $name);

        $result = $schemaFactory->buildTable($table);

        $hookEmitter->run('table.update', $name);
        $hookEmitter->run('table.update:after', $name);

        return $result ? true : false;
    }

    /**
     * @param array $columns
     *
     * @throws InvalidRequestException
     */
    protected function validateSystemFields(array $columns)
    {
        $found = [];

        foreach ($columns as $column) {
            $interface = ArrayUtils::get($column, 'interface');
            if ($this->getSchemaManager()->isSystemField($interface)) {
                if (!isset($found[$interface])) {
                    $found[$interface] = 0;
                }

                $found[$interface]++;
            }
        }

        $interfaces = [];
        foreach ($found as $interface => $count) {
            if ($count > 1) {
                $interfaces[] = $interface;
            }
        }

        if (!empty($interfaces)) {
            throw new InvalidRequestException(
                'Only one system interface permitted per table: ' . implode(', ', $interfaces)
            );
        }
    }

    /**
     * @param array $columns
     *
     * @return array
     *
     * @throws InvalidRequestException
     */
    protected function parseColumns(array $columns)
    {
        $result = [];
        foreach ($columns as $column) {
            if (!isset($column['type']) || !isset($column['name'])) {
                throw new InvalidRequestException(
                    'All column requires a name and a type.'
                );
            }

            $result[$column['name']] = ArrayUtils::omit($column, 'name');
        }
    }
}
