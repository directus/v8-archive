<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Exception\CollectionNotManagedException;
use Directus\Database\Exception\FieldAlreadyExistsException;
use Directus\Database\Exception\FieldNotFoundException;
use Directus\Database\Exception\FieldNotManagedException;
use Directus\Database\Exception\CollectionAlreadyExistsException;
use Directus\Database\Exception\CollectionNotFoundException;
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

        return $tableGateway->getItems($params);
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

        //  GET NOT MANAGED FIELDS
        if (!empty($result['data'])) {
            $result['data'] = $this->parseSchemaFieldsMissing($collection, ArrayUtils::get($result, 'data'));
        }

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

        return $tableGateway->getItemsByIds($name, $params);
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
        } else {
            //  Get not managed fields
            $result = ['data' => $this->parseSchemaField($columnObject)];
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

        //  GET NOT MANAGED FIELDS
        if (empty($result['data'])) {
            $result['data'] = $this->parseSchemaFields($collection, ArrayUtils::get($result, 'data'), $fieldsName);
        }

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

        if(!$this->hasPrimaryField($data['fields'])){
            throw new BadRequestException("Collection does not have a primary key.");
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
        // TODO: Let's make this info a string ALL the time at this level
        $options = $this->parseOptions(ArrayUtils::get($data, 'options'));

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

        if (ArrayUtils::has($data, 'options')) {
            $data['options'] = $this->parseOptions($data['options']);
        }

        $collectionObject = $this->getSchemaManager()->getCollection('directus_fields');

        return $this->getFieldsTableGateway()->updateRecord(
            ArrayUtils::pick($data, $collectionObject->getFieldsName())
        );
    }

    /**
     * Parse Fields options column
     *
     * NOTE: This is temporary until system fields has the interfaces set
     *
     * @param $options
     *
     * @return null|string
     */
    protected function parseOptions($options)
    {
        if (is_array($options) && !empty($options)) {
            $options = json_encode($options);
        } else {
            $options = null;
        }

        return $options;
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

    public function hasPrimaryField($fields){
        $result = false;

        foreach($fields as $field){
            if($field[primary_key] == true){
                $result = true;
                break;
            }
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
     * Parses a list of Schema Attributes into Directus Attributes
     *
     * @param Collection $collection
     * @param array $fieldsData
     * @param array $fieldsName
     *
     * @return array
     */
    protected function parseSchemaFields(Collection $collection, array $fieldsData, array $fieldsName = [])
    {
        $result = [];

        if (empty($fieldsName)) {
            $fieldsName = ArrayUtils::pluck($fieldsData, 'field');
        }

        if (empty($fieldsName)) {
            return $fieldsData;
        }

        $fields = $collection->getFields(
            $fieldsName
        );

        foreach ($fields as $field) {
            $result[] = $this->parseSchemaField($field);
        }

        return array_merge($fieldsData, $result);
    }

    /**
     * Parses a list of missing Schema Attributes into Directus Attributes
     *
     * @param Collection $collection
     * @param array $fieldsData
     *
     * @return array
     */
    protected function parseSchemaFieldsMissing(Collection $collection, array $fieldsData)
    {
        $result = [];

        $fields = $collection->getFieldsNotIn(
            ArrayUtils::pluck($fieldsData, 'field')
        );

        foreach ($fields as $field) {
            $result[] = $this->parseSchemaField($field);
        }

        return array_merge($fieldsData, $result);
    }

    /**
     * Parses Schema Attributes into Directus Attributes
     *
     * @param Field $field
     *
     * @return array|mixed
     */
    protected function parseSchemaField(Field $field)
    {
        $tableGateway = $this->getFieldsTableGateway();
        $fieldsName = $tableGateway->getTableSchema()->getFieldsName();

        $data = ArrayUtils::pick($field->toArray(), array_merge($fieldsName, ['managed']));
        // it must be not managed
        $data['managed'] = false;
        $data['primary_key'] = $field->hasPrimaryKey();

        return $tableGateway->parseRecord($data);
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
}
