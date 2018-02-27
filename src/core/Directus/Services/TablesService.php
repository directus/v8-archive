<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Exception\ColumnAlreadyExistsException;
use Directus\Database\Exception\ColumnNotFoundException;
use Directus\Database\Exception\TableAlreadyExistsException;
use Directus\Database\Exception\TableNotFoundException;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\SchemaFactory;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableSchema;
use Directus\Exception\BadRequestException;
use Directus\Exception\ErrorException;
use Directus\Exception\UnauthorizedException;
use Directus\Hook\Emitter;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;
use Directus\Validator\Exception\InvalidRequestException;

// TODO: Create activity for collection and fields
class TablesService extends AbstractService
{
    /**
     * @var string
     */
    protected $collection;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->collection = SchemaManager::TABLE_COLLECTIONS;
    }

    public function findAll(array $params = [])
    {
        // $tables = TableSchema::getTablenames($params);

        $tableGateway = $this->createTableGateway($this->collection);

        return $tableGateway->getItems($params);
    }

    public function findAllFields($collectionName, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        // $this->tagResponseCache('tableColumnsSchema_'.$tableName);

        $this->validate(['collection' => $collectionName], ['collection' => 'required|string']);

        $tableGateway = $this->createTableGateway('directus_fields');

        return $tableGateway->getItems(array_merge($params, [
            'filter' => [
                'collection' => $collectionName
            ]
        ]));
    }

    public function find($name, array $params = [])
    {
        $this->validate(['collection' => $name], ['collection' => 'required|string']);

        $tableGateway = $this->createTableGateway($this->collection);

        return $tableGateway->getItems(array_merge($params, [
            'id' => $name
        ]));
    }

    public function findField($collection, $field, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $this->validate([
            'collection' => $collection,
            'field' => $field
        ], [
            'collection' => 'required|string',
            'field' => 'required|string'
        ]);

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $collectionObject = $schemaManager->getTableSchema($collection);
        if (!$collectionObject) {
            throw new TableNotFoundException($collection);
        }

        $columnObject = $collectionObject->getField($field);
        if (!$columnObject) {
            throw new ColumnNotFoundException($field);
        }

        $tableGateway = $this->createTableGateway('directus_fields');

        $params = ArrayUtils::pick($params, ['meta', 'fields']);
        $params['single'] = true;
        $params['filter'] = [
            'collection' => $collection,
            'field' => $field
        ];

        return $tableGateway->getItems($params);
    }

    public function deleteField($collection, $field, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $this->enforcePermissions('directus_fields', [], $params);

        $this->validate([
            'collection' => $collection,
            'field' => $field
        ], [
            'collection' => 'required|string',
            'field' => 'required|string'
        ]);

        $tableService = new TablesService($this->container);

        $tableService->dropColumn($collection, $field);

        return true;
    }

    /**
     *
     * @param string $name
     * @param array $data
     * @param array $params
     *
     * @return array
     *
     * @throws ErrorException
     * @throws InvalidRequestException
     * @throws TableAlreadyExistsException
     * @throws UnauthorizedException
     */
    public function createTable($name, array $data = [], array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Authorized to create collections');
        }

        $this->enforcePermissions($this->collection, $data, $params);

        $data['collection'] = $name;
        $collectionName = 'directus_collections';
        $collectionObject = $this->getSchemaManager()->getTableSchema($collectionName);
        $constraints = $this->createConstraintFor($collectionName, $collectionObject->getFieldsName());
        // TODO: Default to primary key id
        $constraints['fields'][] = 'required';
        $this->validate($data, array_merge(['fields' => 'array'], $constraints));

        // ----------------------------------------------------------------------------

        if ($this->getSchemaManager()->tableExists($name)) {
            throw new TableAlreadyExistsException($name);
        }

        if (!$this->isValidName($name)) {
            throw new InvalidRequestException('Invalid collection name');
        }

        $success = $this->createTableSchema($name, $data);
        if (!$success) {
            throw new ErrorException('Error creating the collection');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_collections');

        $columns = ArrayUtils::get($data, 'fields');
        $this->addColumnsInfo($name, $columns);

        $item = ArrayUtils::omit($data, 'fields');
        $item['collection'] = $name;

        $table = $collectionsTableGateway->updateRecord($item);

        // ----------------------------------------------------------------------------

        $collectionTableGateway = $this->createTableGateway($collectionName);
        $tableData = $collectionTableGateway->parseRecord($table->toArray());

        return $collectionTableGateway->wrapData($tableData, true, ArrayUtils::get($params, 'meta'));
    }

    /**
     * Updates a table
     *
     * @param $name
     * @param array $data
     * @param array $params
     *
     * @return array
     *
     * @throws ErrorException
     * @throws TableNotFoundException
     * @throws UnauthorizedException
     */
    public function updateTable($name, array $data, array $params = [])
    {
        // TODO: Add only for admin middleware
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $data = ArrayUtils::omit($data, 'collection');

        $this->enforcePermissions($this->collection, $data, $params);

        // Validates the collection name
        $this->validate(['collection' => $name], ['collection' => 'required|string']);

        // Validates payload data
        $collectionObject = $this->getSchemaManager()->getTableSchema($this->collection);
        $constraints = $this->createConstraintFor($this->collection, $collectionObject->getFieldsName());
        $data['collection'] = $name;
        $this->validate($data, array_merge(['fields' => 'array'], $constraints));

        // TODO: Create a check if exists method (quicker) + not found exception
        $tableGateway = $this->createTableGateway($this->collection);
        $tableGateway->loadItems(['id' => $name]);
        // ----------------------------------------------------------------------------

        if (!$this->getSchemaManager()->tableExists($name)) {
            throw new TableNotFoundException($name);
        }

        $tableObject = $this->getSchemaManager()->getTableSchema($name);
        $columns = ArrayUtils::get($data, 'fields', []);
        foreach ($columns as $i => $column) {
            $columnObject = $tableObject->getField($column['field']);
            if ($columnObject) {
                $currentColumnData = $columnObject->toArray();
                $columns[$i] = array_merge($currentColumnData, $columns[$i]);
            }
        }

        $data['fields'] = $columns;
        $success = $this->updateTableSchema($name, $data);
        if (!$success) {
            throw new ErrorException('Error creating the table');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_collections');

        $columns = ArrayUtils::get($data, 'fields', []);
        if (!empty($columns)) {
            $this->addColumnsInfo($name, $columns);
        }

        $item = ArrayUtils::omit($data, 'fields');
        $item['collection'] = $name;

        $collection = $collectionsTableGateway->updateRecord($item);

        // ----------------------------------------------------------------------------
        return $tableGateway->wrapData(
            $collection->toArray(),
            true,
            ArrayUtils::get($params, 'meta')
        );
    }

    public function delete($name, array $params = [])
    {
        $this->enforcePermissions($this->collection, [], $params);
        $this->validate(['name' => $name], ['name' => 'required|string']);
        // TODO: How are we going to handle unmanage
        // $unmanaged = $request->getQueryParam('unmanage', 0);
        // if ($unmanaged == 1) {
        //     $tableGateway = new RelationalTableGateway($tableName, $dbConnection, $acl);
        //     $success = $tableGateway->stopManaging();
        // }

        return $this->dropTable($name);
    }

    /**
     * Adds a column to an existing table
     *
     * @param string $collectionName
     * @param string $columnName
     * @param array $data
     * @param array $params
     *
     * @return array
     *
     * @throws ColumnAlreadyExistsException
     * @throws TableNotFoundException
     * @throws UnauthorizedException
     */
    public function addColumn($collectionName, $columnName, array $data, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $this->enforcePermissions('directus_fields', $data, $params);

        $data['field'] = $columnName;
        $data['collection'] = $collectionName;
        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $collectionObject = $this->getSchemaManager()->getTableSchema('directus_fields');
        $constraints = $this->createConstraintFor('directus_fields', $collectionObject->getFieldsName());
        $this->validate(array_merge($data, ['collection' => $collectionName]), array_merge(['collection' => 'required|string'], $constraints));

        // ----------------------------------------------------------------------------

        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getField($columnName);
        if ($columnObject) {
            throw new ColumnAlreadyExistsException($columnName);
        }

        $columnData = array_merge($data, [
            'field' => $columnName
        ]);

        $this->updateTableSchema($collectionName, [
            'fields' => [$columnData]
        ]);

        // ----------------------------------------------------------------------------

        $field = $this->addColumnInfo($collectionName, $columnData);

        return $this->createTableGateway('directus_fields')->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
        );
    }

    /**
     * Adds a column to an existing table
     *
     * @param string $collectionName
     * @param string $columnName
     * @param array $data
     * @param array $params
     *
     * @return array
     *
     * @throws ColumnNotFoundException
     * @throws TableNotFoundException
     * @throws UnauthorizedException
     */
    public function changeColumn($collectionName, $columnName, array $data, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $this->enforcePermissions('directus_fields', $data, $params);

        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $this->validate([
            'collection' => $collectionName,
            'field' => $columnName,
            'payload' => $data
        ], [
            'collection' => 'required|string',
            'field' => 'required|string',
            'payload' => 'required|array'
        ]);

        // Remove field from data
        ArrayUtils::pull($data, 'field');

        // ----------------------------------------------------------------------------

        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getField($columnName);
        if (!$columnObject) {
            throw new ColumnNotFoundException($columnName);
        }

        $columnData = array_merge($columnObject->toArray(), $data);
        $this->updateTableSchema($collectionName, [
            'fields' => [$columnData]
        ]);

        // $this->invalidateCacheTags(['tableColumnsSchema_'.$tableName, 'columnSchema_'.$tableName.'_'.$columnName]);
        $field = $this->addColumnInfo($collectionName, $columnData);
        // ----------------------------------------------------------------------------

        return $this->createTableGateway('directus_fields')->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
        );
    }

    public function dropColumn($collectionName, $fieldName)
    {
        $tableObject = $this->getSchemaManager()->getTableSchema($collectionName);
        if (!$tableObject) {
            throw new TableNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getField($fieldName);
        if (!$columnObject) {
            throw new ColumnNotFoundException($fieldName);
        }

        if (count($tableObject->getFields()) === 1) {
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
        $options = ArrayUtils::get($column, 'options');

        $data = [
            'collection' => $collectionName,
            'field' => $column['field'],
            'type' => $column['type'],
            'interface' => $column['interface'],
            'required' => ArrayUtils::get($column, 'required', false),
            'sort' => ArrayUtils::get($column, 'sort', false),
            'comment' => ArrayUtils::get($column, 'comment', false),
            'hidden_input' => ArrayUtils::get($column, 'hidden_input', false),
            'hidden_list' => ArrayUtils::get($column, 'hidden_list', false),
            'options' => $options ? json_encode($options) : $options
        ];

        $fieldsTableGateway = $this->createTableGateway('directus_fields');
        $row = $fieldsTableGateway->findOneByArray([
            'collection' => $collectionName,
            'field' => $column['field']
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
        $fieldsTableGateway = $this->createTableGateway('directus_fields');

        return $fieldsTableGateway->delete([
            'collection' => $collectionName,
            'field' => $fieldName
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
     * @return \Directus\Database\Object\Collection
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

        $columns = ArrayUtils::get($data, 'fields', []);
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

        $columns = ArrayUtils::get($data, 'fields', []);
        $this->validateSystemFields($columns);

        $toAdd = $toChange = $aliasColumn = [];
        $tableObject = $this->getSchemaManager()->getTableSchema($name);
        foreach ($columns as $i => $column) {
            $columnObject = $tableObject->getField($column['field']);
            $type = ArrayUtils::get($column, 'type');
            if ($columnObject) {
                $toChange[] = array_merge($columnObject->toArray(), $column);
            } else if (strtoupper($type) !== 'ALIAS') {
                $toAdd[] = $column;
            } else {
                $aliasColumn[] = $column;
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
        $this->updateColumnsRelation($name, array_merge($toAdd, $toChange, $aliasColumn));

        $hookEmitter->run('table.update', $name);
        $hookEmitter->run('table.update:after', $name);

        return $result ? true : false;
    }

    protected function updateColumnsRelation($collectionName, array $columns)
    {
        $result = [];
        foreach ($columns as $column) {
            $result[] = $this->updateColumnRelation($collectionName, $column);
        }

        return $result;
    }

    protected function updateColumnRelation($collectionName, array $column)
    {
        $relationData = ArrayUtils::get($column, 'relation', []);
        if (!$relationData) {
            return false;
        }

        $relationshipType = ArrayUtils::get($relationData, 'relationship_type', '');
        $collectionBName = ArrayUtils::get($relationData, 'collection_b');
        $storeCollectionName = ArrayUtils::get($relationData, 'store_collection');
        $collectionBObject = $this->getSchemaManager()->getTableSchema($collectionBName);
        $relationsTableGateway = $this->createTableGateway('directus_relations');

        $data = [];
        switch ($relationshipType) {
            case FieldRelationship::MANY_TO_ONE:
                $data['relationship_type'] = FieldRelationship::MANY_TO_ONE;
                $data['collection_a'] = $collectionName;
                $data['collection_b'] = $collectionBName;
                $data['store_key_a'] = $column['field'];
                $data['store_key_b'] = $collectionBObject->getPrimaryColumn();
                break;
            case FieldRelationship::ONE_TO_MANY:
                $data['relationship_type'] = FieldRelationship::ONE_TO_MANY;
                $data['collection_a'] = $collectionName;
                $data['collection_b'] = $collectionBName;
                $data['store_key_a'] = $collectionBObject->getPrimaryColumn();
                $data['store_key_b'] = $column['field'];
                break;
            case FieldRelationship::MANY_TO_MANY:
                $data['relationship_type'] = FieldRelationship::MANY_TO_MANY;
                $data['collection_a'] = $collectionName;
                $data['store_collection'] = $storeCollectionName;
                $data['collection_b'] = $collectionBName;
                $data['store_key_a'] = $relationData['store_key_a'];
                $data['store_key_b'] = $relationData['store_key_b'];
                break;
        }


        $row = $relationsTableGateway->findOneByArray([
            'collection_a' => $collectionName,
            'store_key_a' => $column['field']
        ]);

        if ($row) {
            $data['id'] = $row['id'];
        }

        return $relationsTableGateway->updateRecord($data);
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
            if (!isset($column['type']) || !isset($column['field'])) {
                throw new InvalidRequestException(
                    'All column requires a name and a type.'
                );
            }

            $result[$column['field']] = ArrayUtils::omit($column, 'field');
        }
    }
}
