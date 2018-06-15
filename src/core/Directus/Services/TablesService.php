<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Exception\CollectionNotManagedException;
use Directus\Database\Exception\FieldAlreadyExistsException;
use Directus\Database\Exception\FieldNotFoundException;
use Directus\Database\Exception\FieldNotManagedException;
use Directus\Database\Exception\CollectionAlreadyExistsException;
use Directus\Database\Exception\CollectionNotFoundException;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\DataTypes;
use Directus\Database\Schema\Object\Collection;
use Directus\Database\Schema\Object\Field;
use Directus\Database\Schema\Object\FieldRelationship;
use Directus\Database\Schema\SchemaFactory;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\SchemaService;
use Directus\Database\TableGateway\RelationalTableGateway;
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

    /**
     * @var RelationalTableGateway
     */
    protected $fieldsTableGateway;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->collection = SchemaManager::COLLECTION_COLLECTIONS;
    }

    public function findAll(array $params = [])
    {
        $tableGateway = $this->createTableGateway($this->collection);

        $result = $tableGateway->getItems($params);

        $result['data'] = $this->mergeSchemaCollections($result['data']);

        return $result;
    }

    public function findAllFields($collectionName, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        // $this->tagResponseCache('tableColumnsSchema_'.$tableName);

        $this->validate(['collection' => $collectionName], ['collection' => 'required|string']);

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $collection = $schemaManager->getCollection($collectionName);
        if (!$collection) {
            throw new CollectionNotFoundException($collectionName);
        }

        $tableGateway = $this->getFieldsTableGateway();
        $result = $tableGateway->getItems(array_merge($params, [
            'filter' => [
                'collection' => $collectionName
            ]
        ]));

        $result['data'] = $this->mergeMissingSchemaFields($collection, $result['data']);

        return $result;
    }

    public function find($name, array $params = [])
    {
        $this->validate(['collection' => $name], ['collection' => 'required|string']);

        $tableGateway = $this->createTableGateway($this->collection);

        return $tableGateway->getItems(array_merge($params, [
            'id' => $name
        ]));
    }

    public function findByIds($name, array $params = [])
    {
        $this->validate(['collection' => $name], ['collection' => 'required|string']);

        $tableGateway = $this->createTableGateway($this->collection);

        try {
            $result = $tableGateway->getItemsByIds($name, $params);
            $collectionNames = StringUtils::csv((string) $name);
            $result['data'] = $this->mergeMissingSchemaCollections($collectionNames, $result['data']);
        } catch (ItemNotFoundException $e) {
            $data = $this->mergeSchemaCollection($name, []);

            $result = $tableGateway->wrapData($data, true, ArrayUtils::get($params, 'meta'));
        }

        return $result;
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
        $collectionObject = $schemaManager->getCollection($collection);
        if (!$collectionObject) {
            throw new CollectionNotFoundException($collection);
        }

        $columnObject = $collectionObject->getField($field);
        if (!$columnObject) {
            throw new FieldNotFoundException($field);
        }

        $tableGateway = $this->getFieldsTableGateway();

        if ($columnObject->isManaged()) {
            $params = ArrayUtils::pick($params, ['meta', 'fields']);
            $params['single'] = true;
            $params['filter'] = [
                'collection' => $collection,
                'field' => $field
            ];

            $result = $tableGateway->getItems($params);
            $fieldData = $this->mergeMissingSchemaField($collectionObject, $result['data']);
            if ($fieldData) {
                $result['data'] = $fieldData;
            }
        } else {
            //  Get not managed fields
            $result = $tableGateway->wrapData(
                $this->mergeSchemaField($columnObject),
                true,
                ArrayUtils::pick($params, 'meta')
            );
        }

        return $result;
    }

    public function findFields($collectionName, array $fieldsName, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        // $this->tagResponseCache('tableColumnsSchema_'.$tableName);
        $this->validate(['fields' => $fieldsName], ['fields' => 'required|array']);

        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $collection = $schemaManager->getCollection($collectionName);
        if (!$collection) {
            throw new CollectionNotFoundException($collectionName);
        }

        $tableGateway = $this->getFieldsTableGateway();
        $result = $tableGateway->getItems(array_merge($params, [
            'filter' => [
                'collection' => $collectionName,
                'field' => ['in' => $fieldsName]
            ]
        ]));

        $result['data'] = $this->mergeMissingSchemaFields($collection, ArrayUtils::get($result, 'data'), $fieldsName);

        return $result;
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
     * @throws CollectionAlreadyExistsException
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    public function createTable($name, array $data = [], array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Authorized to create collections');
        }

        $this->enforcePermissions($this->collection, $data, $params);

        $data['collection'] = $name;
        $collectionsCollectionName = 'directus_collections';
        $collectionsCollectionObject = $this->getSchemaManager()->getCollection($collectionsCollectionName);
        $constraints = $this->createConstraintFor($collectionsCollectionName, $collectionsCollectionObject->getFieldsName());

        $this->validate($data, array_merge(['fields' => 'array'], $constraints));

        if (!$this->isValidName($name)) {
            throw new InvalidRequestException('Invalid collection name');
        }

        $collection = null;

        try {
            $collection = $this->getSchemaManager()->getCollection($name);
        } catch (CollectionNotFoundException $e) {
            // TODO: Default to primary key id
            $constraints['fields'][] = 'required';

            $this->validate($data, array_merge(['fields' => 'array'], $constraints));
        }

        // ----------------------------------------------------------------------------

        if ($collection && $collection->isManaged()) {
            throw new CollectionAlreadyExistsException($name);
        }

        if (!$this->hasPrimaryField($data['fields'])) {
            throw new BadRequestException('Collection does not have a primary key field.');
        }

        if (!$this->hasUniquePrimaryField($data['fields'])) {
            throw new BadRequestException('Collection must only have one primary key field.');
        }

        if (!$this->hasUniqueAutoIncrementField($data['fields'])) {
            throw new BadRequestException('Collection must only have one auto increment field.');
        }

        if (!$this->hasUniqueFieldsName($data['fields'])) {
            throw new BadRequestException('Collection fields name must be unique.');
        }

        if ($collection && !$collection->isManaged()) {
            $success = $this->updateTableSchema($collection, $data);
        } else {
            $success = $this->createTableSchema($name, $data);
        }

        if (!$success) {
            throw new ErrorException('Error creating the collection');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_collections');

        $fields = ArrayUtils::get($data, 'fields');
        if ($collection && !$collection->isManaged() && !$fields) {
            $fields = $collection->getFieldsArray();
        }

        $this->addColumnsInfo($name, $fields);

        $item = ArrayUtils::omit($data, 'fields');
        $item['collection'] = $name;

        $table = $collectionsTableGateway->updateRecord($item);

        // ----------------------------------------------------------------------------

        $collectionTableGateway = $this->createTableGateway($collectionsCollectionName);
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
     * @throws CollectionNotManagedException
     * @throws ErrorException
     * @throws CollectionNotFoundException
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
        $collectionsCollectionObject = $this->getSchemaManager()->getCollection($this->collection);
        $constraints = $this->createConstraintFor($this->collection, $collectionsCollectionObject->getFieldsName());
        $data['collection'] = $name;
        $this->validate($data, array_merge(['fields' => 'array'], $constraints));

        $collectionObject = $this->getSchemaManager()->getCollection($name);
        if (!$collectionObject->isManaged()) {
            throw new CollectionNotManagedException($collectionObject->getName());
        }

        // TODO: Create a check if exists method (quicker) + not found exception
        $tableGateway = $this->createTableGateway($this->collection);
        $tableGateway->getOneData($name);
        // ----------------------------------------------------------------------------

        if (!$this->getSchemaManager()->tableExists($name)) {
            throw new CollectionNotFoundException($name);
        }

        $collection = $this->getSchemaManager()->getCollection($name);
        $fields = ArrayUtils::get($data, 'fields', []);
        foreach ($fields as $i => $field) {
            $field = $collection->getField($field['field']);
            if ($field) {
                $currentColumnData = $field->toArray();
                $fields[$i] = array_merge($currentColumnData, $fields[$i]);
            }
        }

        $data['fields'] = $fields;
        $success = $this->updateTableSchema($collection, $data);
        if (!$success) {
            throw new ErrorException('Error updating the collection');
        }

        $collectionsTableGateway = $this->createTableGateway('directus_collections');
        if (!empty($fields)) {
            $this->addColumnsInfo($name, $fields);
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
     * @throws FieldAlreadyExistsException
     * @throws CollectionNotFoundException
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
        $collectionObject = $this->getSchemaManager()->getCollection('directus_fields');
        $constraints = $this->createConstraintFor('directus_fields', $collectionObject->getFieldsName());
        $this->validate(array_merge($data, ['collection' => $collectionName]), array_merge(['collection' => 'required|string'], $constraints));

        // ----------------------------------------------------------------------------

        $collection = $this->getSchemaManager()->getCollection($collectionName);
        if (!$collection) {
            throw new CollectionNotFoundException($collectionName);
        }

        $field = $collection->getField($columnName);
        if ($field && $field->isManaged()) {
            throw new FieldAlreadyExistsException($columnName);
        }

        $columnData = array_merge($data, [
            'field' => $columnName
        ]);

        // TODO: Only call this when necessary
        $this->updateTableSchema($collection, [
            'fields' => [$columnData]
        ]);

        // ----------------------------------------------------------------------------

        $field = $this->addFieldInfo($collectionName, $columnName, $columnData);

        return $this->getFieldsTableGateway()->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
        );
    }

    /**
     * Adds a column to an existing table
     *
     * @param string $collectionName
     * @param string $fieldName
     * @param array $data
     * @param array $params
     *
     * @return array
     *
     * @throws FieldNotFoundException
     * @throws FieldNotManagedException
     * @throws CollectionNotFoundException
     * @throws UnauthorizedException
     */
    public function changeColumn($collectionName, $fieldName, array $data, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Permission denied');
        }

        $this->enforcePermissions('directus_fields', $data, $params);

        // TODO: Length is required by some data types, which make this validation not working fully for columns
        // TODO: Create new constraint that validates the column data type to be one of the list supported
        $this->validate([
            'collection' => $collectionName,
            'field' => $fieldName,
            'payload' => $data
        ], [
            'collection' => 'required|string',
            'field' => 'required|string',
            'payload' => 'required|array'
        ]);

        // Remove field from data
        ArrayUtils::pull($data, 'field');

        // ----------------------------------------------------------------------------

        $collection = $this->getSchemaManager()->getCollection($collectionName);
        if (!$collection) {
            throw new CollectionNotFoundException($collectionName);
        }

        $field = $collection->getField($fieldName);
        if (!$field) {
            throw new FieldNotFoundException($fieldName);
        }

        if (!$field->isManaged()) {
            throw new FieldNotManagedException($field->getName());
        }

        // TODO: Only update schema when is needed
        $fieldData = array_merge($field->toArray(), $data);
        $this->updateTableSchema($collection, [
            'fields' => [$fieldData]
        ]);

        // $this->invalidateCacheTags(['tableColumnsSchema_'.$tableName, 'columnSchema_'.$tableName.'_'.$columnName]);
        $field = $this->addOrUpdateFieldInfo($collectionName, $fieldName, $data);
        // ----------------------------------------------------------------------------

        return $this->getFieldsTableGateway()->wrapData(
            $field->toArray(),
            true,
            ArrayUtils::get($params, 'meta', 0)
        );
    }

    public function dropColumn($collectionName, $fieldName)
    {
        $tableObject = $this->getSchemaManager()->getCollection($collectionName);
        if (!$tableObject) {
            throw new CollectionNotFoundException($collectionName);
        }

        $columnObject = $tableObject->getField($fieldName);
        if (!$columnObject) {
            throw new FieldNotFoundException($fieldName);
        }

        if (count($tableObject->getFields()) === 1) {
            throw new BadRequestException('Cannot delete the last field');
        }

        if (!$columnObject->isAlias()) {
            if (!$this->dropColumnSchema($collectionName, $fieldName)) {
                throw new ErrorException('Error deleting the field');
            }
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
            $resultsSet[] = $this->addOrUpdateFieldInfo($collectionName, $column['field'], $column);
        }

        return $resultsSet;
    }

    /**
     * Adds or update a field data
     *
     * @param string $collection
     * @param string $field
     * @param array $data
     *
     * @return BaseRowGateway
     */
    protected function addOrUpdateFieldInfo($collection, $field, array $data)
    {
        $fieldsTableGateway = $this->getFieldsTableGateway();
        $row = $fieldsTableGateway->findOneByArray([
            'collection' => $collection,
            'field' => $field
        ]);

        if ($row) {
            $field = $this->updateFieldInfo($row['id'], $data);
        } else {
            $field = $this->addFieldInfo($collection, $field, $data);
        }

        return $field;
    }

    protected function addFieldInfo($collection, $field, array $data)
    {
        $defaults = [
            'collection' => $collection,
            'field' => $field,
            'type' => null,
            'interface' => null,
            'required' => false,
            'sort' => 0,
            'note' => null,
            'hidden_input' => 0,
            'hidden_list' => 0,
            'options' => null
        ];

        $data = array_merge($defaults, $data, [
            'collection' => $collection,
            'field' => $field,
            'options' => $options
        ]);

        $collectionObject = $this->getSchemaManager()->getCollection('directus_fields');

        return $this->getFieldsTableGateway()->updateRecord(
            ArrayUtils::pick($data, $collectionObject->getFieldsName())
        );
    }

    protected function updateFieldInfo($id, array $data)
    {
        ArrayUtils::remove($data, ['collection', 'field']);
        $data['id'] = $id;

        $collectionObject = $this->getSchemaManager()->getCollection('directus_fields');

        return $this->getFieldsTableGateway()->updateRecord(
            ArrayUtils::pick($data, $collectionObject->getFieldsName())
        );
    }

    /**
     * @param $collectionName
     * @param $fieldName
     *
     * @return int
     */
    public function removeColumnInfo($collectionName, $fieldName)
    {
        $fieldsTableGateway = $this->getFieldsTableGateway();

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
     * @throws CollectionNotFoundException
     */
    public function dropTable($name)
    {
        if (!$this->getSchemaManager()->tableExists($name)) {
            throw new CollectionNotFoundException($name);
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
        return SchemaService::getCollection($tableName);
    }

    /**
     * Checks that at least one of the fields has primary_key set to true.
     *
     * @param array $fields
     *
     * @return bool
     */
    public function hasPrimaryField(array $fields)
    {
        return $this->hasFieldAttributeWith($fields, 'primary_key', true, null, 1);
    }

    /**
     * Checks that a maximum of 1 field has the primary_key field set to true. This will succeed if there are 0
     * or 1 fields set as the primary key.
     *
     * @param array $fields
     *
     * @return bool
     */
    public function hasUniquePrimaryField(array $fields)
    {
        return $this->hasFieldAttributeWith($fields, 'primary_key', true);
    }

    /**
     * Checks that at most one of the fields has auto_increment set to true
     *
     * @param array $fields
     *
     * @return bool
     */
    public function hasUniqueAutoIncrementField(array $fields)
    {
        return $this->hasFieldAttributeWith($fields, 'auto_increment', true);
    }

    /**
     * Checks that all fields name are unique
     *
     * @param array $fields
     *
     * @return bool
     */
    public function hasUniqueFieldsName(array $fields)
    {
        $fieldsName = [];
        $unique = true;

        foreach ($fields as $field) {
            $fieldName = ArrayUtils::get($field, 'field');
            if (in_array($fieldName, $fieldsName)) {
                $unique = false;
                break;
            }

            $fieldsName[] = $fieldName;
        }

        return $unique;
    }

    /**
     * Checks that a set of fields has at least $min and at most $max attribute with the value of $value
     *
     * @param array $fields
     * @param string  $attribute
     * @param mixed $value
     * @param int $max
     * @param int $min
     *
     * @return bool
     */
    protected function hasFieldAttributeWith(array $fields, $attribute, $value, $max = 1, $min = 0)
    {
        $count = 0;
        $result = false;

        foreach ($fields as $field) {
            if (ArrayUtils::get($field, $attribute) === $value) {
                $count++;
            }
        }

        $ignoreMax = is_null($max);
        $ignoreMin = is_null($min);

        if ($ignoreMax && $ignoreMin) {
            $result = $count >= 1;
        } else if ($ignoreMax) {
            $result = $count >= $min;
        } else if ($ignoreMin) {
            $result = $count <= $max;
        } else {
            $result = $count >= $min && $count <= $max;
        }

        return $result;
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
        $hookEmitter->run('collection.create:before', $name);

        $result = $schemaFactory->buildTable($table);

        $hookEmitter->run('collection.create', $name);
        $hookEmitter->run('collection.create:after', $name);

        return $result ? true : false;
    }

    /**
     * @param Collection $collection
     * @param array $data
     *
     * @return bool
     */
    protected function updateTableSchema(Collection $collection, array $data)
    {
        /** @var SchemaFactory $schemaFactory */
        $schemaFactory = $this->container->get('schema_factory');
        $name = $collection->getName();

        $fields = ArrayUtils::get($data, 'fields', []);
        $this->validateSystemFields($fields);

        $toAdd = $toChange = $toDrop = [];
        foreach ($fields as $fieldData) {
            $field = $collection->getField($fieldData['field']);

            if ($field) {
                if (!$field->isAlias() && DataTypes::isAliasType(ArrayUtils::get($fieldData, 'type'))) {
                    $toDrop[] = $field->getName();
                } else if ($field->isAlias() && !DataTypes::isAliasType(ArrayUtils::get($fieldData, 'type'))) {
                    $toAdd[] = array_merge($field->toArray(), $fieldData);
                } else {
                    $toChange[] = array_merge($field->toArray(), $fieldData);
                }
            } else {
                $toAdd[] = $fieldData;
            }
        }

        $table = $schemaFactory->alterTable($name, [
            'add' => $toAdd,
            'change' => $toChange,
            'drop' => $toDrop
        ]);

        /** @var Emitter $hookEmitter */
        $hookEmitter = $this->container->get('hook_emitter');
        $hookEmitter->run('collection.update:before', $name);

        $result = $schemaFactory->buildTable($table);
        $this->updateColumnsRelation($name, array_merge($toAdd, $toChange));

        $hookEmitter->run('collection.update', $name);
        $hookEmitter->run('collection.update:after', $name);

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
        $collectionBObject = $this->getSchemaManager()->getCollection($collectionBName);
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
            $type = ArrayUtils::get($column, 'type');
            if ($this->getSchemaManager()->isUniqueFieldType($type)) {
                if (!isset($found[$type])) {
                    $found[$type] = 0;
                }

                $found[$type]++;
            }
        }

        $types = [];
        foreach ($found as $type=> $count) {
            if ($count > 1) {
                $types[] = $type;
            }
        }

        if (!empty($types)) {
            throw new InvalidRequestException(
                'Only one system field permitted per table: ' . implode(', ', $types)
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

    /**
     * Merges a list of missing Schema Attributes into Directus Attributes
     *
     * @param array $collectionNames
     * @param array $collectionsData
     *
     * @return array
     */
    protected function mergeMissingSchemaCollections(array $collectionNames, array $collectionsData)
    {
        if (!ArrayUtils::isNumericKeys($collectionsData)) {
            return $this->mergeSchemaCollection($collectionNames[0], $collectionsData);
        }

        $collectionsDataNames = ArrayUtils::pluck($collectionsData, 'collection');
        $missingCollectionNames = ArrayUtils::missing($collectionsDataNames, $collectionNames);

        $collectionsData = $this->mergeSchemaCollections($collectionsData);

        foreach ($missingCollectionNames as $name) {
            try {
                $collectionsData[] = $this->mergeSchemaCollection($name, []);
            } catch (CollectionNotFoundException $e) {
                // if the collection doesn't exists don't bother with the exception
                // as this is a "filtering" result
                //  which means getting empty result is okay and expected
            }
        }

        return $collectionsData;
    }

    protected function mergeSchemaCollections(array $collectionsData)
    {
        foreach ($collectionsData as &$collectionData) {
            $collectionData = $this->mergeSchemaCollection($collectionData['collection'], $collectionData);
        }

        return $collectionsData;
    }

    /**
     * Merges a list of missing Schema Attributes into Directus Attributes
     *
     * @param Collection $collection
     * @param array $fieldsData
     * @param array $onlyFields
     *
     * @return array
     */
    protected function mergeMissingSchemaFields(Collection $collection, array $fieldsData, array $onlyFields = null)
    {
        $missingFieldsData = [];
        $missingFields = [];
        $lookForMissingFields = true;
        $fieldsName = ArrayUtils::pluck($fieldsData, 'field');

        if ($lookForMissingFields) {
            $missingFields = $collection->getFieldsNotIn(
                $fieldsName
            );
        }

        foreach ($fieldsData as $key => $fieldData) {
            $result = $this->mergeMissingSchemaField($collection, $fieldData);

            if ($result) {
                $fieldsData[$key] = $result;
            }
        }

        foreach ($missingFields as $missingField) {
            if (!is_array($onlyFields) || in_array($missingField->getName(), $onlyFields)) {
                $missingFieldsData[] = $this->mergeSchemaField($missingField);
            }
        }

        return array_merge($fieldsData, $missingFieldsData);
    }

    /**
     * Merges a Field data with an Field object
     *
     * @param Collection $collection
     * @param array $fieldData
     *
     * @return array
     */
    protected function mergeMissingSchemaField(Collection $collection, array $fieldData)
    {
        $field = $collection->getField(ArrayUtils::get($fieldData, 'field'));

        // if for some reason the field key doesn't exists
        // continue with everything as if nothing has happened
        if (!$field) {
            return null;
        }

        return $this->mergeSchemaField($field, $fieldData);
    }

    /**
     * Parses Schema Attributes into Directus Attributes
     *
     * @param Field $field
     * @param array $fieldData
     *
     * @return array
     */
    protected function mergeSchemaField(Field $field, array $fieldData = [])
    {
        $tableGateway = $this->getFieldsTableGateway();
        $fieldsAttributes = array_merge($tableGateway->getTableSchema()->getFieldsName(), ['managed']);

        $data = ArrayUtils::pick(
            array_merge($field->toArray(), $fieldData),
            $fieldsAttributes
        );

        // it must be not managed
        $data['managed'] = boolval(ArrayUtils::get($data, 'managed'));
        $data['primary_key'] = $field->hasPrimaryKey();

        $result = $tableGateway->parseRecord($data);

        return $result;
    }

    /**
     * Parses Collection Schema Attributes into Directus Attributes
     *
     * @param string $collectionName
     * @param array $collectionData
     *
     * @return array
     */
    protected function mergeSchemaCollection($collectionName, array $collectionData)
    {
        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $collection = $schemaManager->getCollection($collectionName);
        $tableGateway = $this->getCollectionsTableGateway();
        $attributesName = array_merge($tableGateway->getTableSchema()->getFieldsName(), ['managed']);

        $collectionData = array_merge(
            ArrayUtils::pick($collection->toArray(), $attributesName),
            $collectionData
        );

        $collectionData['managed'] = boolval($collectionData['managed']);

        return $tableGateway->parseRecord($collectionData);
    }

    /**
 * @return RelationalTableGateway
 */
    protected function getFieldsTableGateway()
    {
        if (!$this->fieldsTableGateway) {
            $this->fieldsTableGateway = $this->createTableGateway('directus_fields');
        }

        return $this->fieldsTableGateway;
    }

    /**
     * @return RelationalTableGateway
     */
    protected function getCollectionsTableGateway()
    {
        if (!$this->fieldsTableGateway) {
            $this->fieldsTableGateway = $this->createTableGateway('directus_collections');
        }

        return $this->fieldsTableGateway;
    }
}
