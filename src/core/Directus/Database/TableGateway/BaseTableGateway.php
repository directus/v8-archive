<?php

namespace Directus\Database\TableGateway;

use Directus\Config\Config;
use Directus\Container\Container;
use Directus\Database\Exception\InvalidQueryException;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Database\Exception\SuppliedArrayAsColumnValue;
use Directus\Database\Query\Builder;
use Directus\Database\Schema\Object\Collection;
use Directus\Database\RowGateway\BaseRowGateway;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGatewayFactory;
use Directus\Database\TableSchema;
use Directus\Filesystem\Thumbnail;
use Directus\Permissions\Acl;
use Directus\Permissions\Exception\ForbiddenCollectionDeleteException;
use Directus\Permissions\Exception\ForbiddenCollectionUpdateException;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Exception\UnexpectedValueException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\ResultSetInterface;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\SqlInterface;
use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\Feature;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\TableGateway\TableGateway;

class BaseTableGateway extends TableGateway
{
    public $primaryKeyFieldName = null;

    public $memcache;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Hook Emitter Instance
     *
     * @var \Directus\Hook\Emitter
     */
    protected static $emitter = null;

    /**
     * @var Container
     */
    protected static $container;

    /**
     * Acl Instance
     *
     * @var Acl|null
     */
    protected $acl = null;

    /**
     * Schema Manager Instance
     *
     * @var SchemaManager|null
     */
    protected $schemaManager = null;

    /**
     * Table Schema Object
     *
     * @var Collection|null
     */
    protected $tableSchema = null;

    /**
     * Name of the field flag that mark a record as hard-delete
     *
     * Note: temporary is being hold by the base table gateway
     *
     * @var string
     */
    protected $deleteFlag = '.delete';

    /**
     * Constructor
     *
     * @param string $table
     * @param AdapterInterface $adapter
     * @param Acl|null $acl
     * @param Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     * @param ResultSetInterface $resultSetPrototype
     * @param Sql $sql
     * @param string $primaryKeyName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($table, AdapterInterface $adapter, $acl = null, $features = null, ResultSetInterface $resultSetPrototype = null, Sql $sql = null, $primaryKeyName = null)
    {
        // Add table name reference here, so we can fetch the table schema object
        $this->table = $table;
        $this->acl = $acl;

        // @NOTE: temporary, do we need it here?
        if ($this->primaryKeyFieldName === null) {
            if ($primaryKeyName !== null) {
                $this->primaryKeyFieldName = $primaryKeyName;
            } else {
                $tableObject = $this->getTableSchema();
                if ($tableObject->getPrimaryField()) {
                    $this->primaryKeyFieldName = $tableObject->getPrimaryField()->getName();
                }
            }
        }

        // @NOTE: This will be substituted by a new Cache wrapper class
        // $this->memcache = new MemcacheProvider();
        if ($features === null) {
            $features = new Feature\FeatureSet();
        } else if ($features instanceof Feature\AbstractFeature) {
            $features = [$features];
        } else if (is_array($features)) {
            $features = new Feature\FeatureSet($features);
        }

        $rowGatewayPrototype = new BaseRowGateway($this->primaryKeyFieldName, $table, $adapter, $this->acl);
        $rowGatewayFeature = new RowGatewayFeature($rowGatewayPrototype);
        $features->addFeature($rowGatewayFeature);

        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);

        if (static::$container) {
            $this->schemaManager = static::$container->get('schema_manager');
        }
    }

    /**
     * Static Factory Methods
     */

    /**
     * Creates a table gateway based on a table's name
     *
     * Underscore to camelcase table name to namespaced table gateway classname,
     * e.g. directus_users => \Directus\Database\TableGateway\DirectusUsersTableGateway
     *
     * @param string $table
     * @param AdapterInterface $adapter
     * @param null $acl
     *
     * @return RelationalTableGateway
     */
    public static function makeTableGatewayFromTableName($table, $adapter, $acl = null)
    {
        return TableGatewayFactory::create($table, [
            'adapter' => $adapter,
            'acl' => $acl
        ]);
    }

    /**
     * Make a new table gateway
     *
     * @param string $tableName
     * @param AdapterInterface $adapter
     * @param Acl $acl
     *
     * @return BaseTableGateway
     */
    public function makeTable($tableName, $adapter = null, $acl = null)
    {
        $adapter = is_null($adapter) ? $this->adapter : $adapter;
        $acl = is_null($acl) ? $this->acl : $acl;

        return static::makeTableGatewayFromTableName($tableName, $adapter, $acl);
    }

    public function getTableSchema($tableName = null)
    {
        if ($this->tableSchema !== null && ($tableName === null || $tableName === $this->getTable())) {
            return $this->tableSchema;
        }

        if ($tableName === null) {
            $tableName = $this->getTable();
        }

        $skipAcl = $this->acl === null;
        $tableSchema = TableSchema::getTableSchema($tableName, [], false, $skipAcl);

        if ($tableName === $this->getTable()) {
            $this->tableSchema = $tableSchema;
        }

        return $tableSchema;
    }

    /**
     * Gets the column schema (object)
     *
     * @param $columnName
     * @param null $tableName
     *
     * @return \Directus\Database\Object\Field
     */
    public function getColumnSchema($columnName, $tableName = null)
    {
        if ($tableName === null) {
            $tableName = $this->getTable();
        }

        $skipAcl = $this->acl === null;

        return TableSchema::getColumnSchema($tableName, $columnName, false, $skipAcl);
    }

    /**
     * Gets the status column name
     *
     * @return string
     */
    public function getStatusColumnName()
    {
        return $this->getTableSchema()->getStatusColumn();
    }

    public function withKey($key, $resultSet)
    {
        $withKey = [];
        foreach ($resultSet as $row) {
            $withKey[$row[$key]] = $row;
        }
        return $withKey;
    }

    protected function convertResultSetDateTimesTimeZones(array $resultSet, $targetTimeZone, $fields = ['datetime'], $yieldObjects = false)
    {
        foreach ($resultSet as &$result) {
            $result = $this->convertRowDateTimesToTimeZone($result, $targetTimeZone, $fields);
        }
        return $resultSet;
    }

    protected function convertRowDateTimesToTimeZone(array $row, $targetTimeZone, $fields = ['datetime'], $yieldObjects = false)
    {
        foreach ($fields as $field) {
            $col =& $row[$field];
            $datetime = DateUtils::convertUtcDateTimeToTimeZone($col, $targetTimeZone);
            $col = $yieldObjects ? $datetime : $datetime->format('Y-m-d H:i:s T');
        }
        return $row;
    }

    /**
     * Create a new row
     *
     * @param null $table
     * @param null $primaryKeyColumn
     *
     * @return BaseRowGateway
     */
    public function newRow($table = null, $primaryKeyColumn = null)
    {
        $table = is_null($table) ? $this->table : $table;
        $primaryKeyColumn = is_null($primaryKeyColumn) ? $this->primaryKeyFieldName : $primaryKeyColumn;
        $row = new BaseRowGateway($primaryKeyColumn, $table, $this->adapter, $this->acl);

        return $row;
    }

    public function find($id, $pk_field_name = null)
    {
        if ($pk_field_name == null) {
            $pk_field_name = $this->primaryKeyFieldName;
        }

        $record = $this->findOneBy($pk_field_name, $id);

        return $record ? $this->parseRecordValuesByType($record) : null;
    }

    public function fetchAll($selectModifier = null)
    {
        return $this->select(function (Select $select) use ($selectModifier) {
            if (is_callable($selectModifier)) {
                $selectModifier($select);
            }
        });
    }

    /**
     * @return array All rows in array form with record IDs for the array's keys.
     */
    public function fetchAllWithIdKeys($selectModifier = null)
    {
        $allWithIdKeys = [];
        $all = $this->fetchAll($selectModifier)->toArray();
        return $this->withKey('id', $all);
    }

    public function findOneBy($field, $value)
    {
        $rowset = $this->ignoreFilters()->select(function (Select $select) use ($field, $value) {
            $select->limit(1);
            $select->where->equalTo($field, $value);
        });

        $row = $rowset->current();
        // Supposing this "one" doesn't exist in the DB
        if (!$row) {
            return false;
        }

        return $row->toArray();
    }

    public function findOneByArray(array $data)
    {
        $rowset = $this->select($data);

        $row = $rowset->current();
        // Supposing this "one" doesn't exist in the DB
        if (!$row) {
            return false;
        }

        return $row->toArray();
    }

    public function addOrUpdateRecordByArray(array $recordData, $tableName = null)
    {
        $tableName = is_null($tableName) ? $this->table : $tableName;
        $collectionObject = $this->getTableSchema($tableName);
        foreach ($recordData as $columnName => $columnValue) {
            $fieldObject = $collectionObject->getField($columnName);
            // TODO: Should this be validate in here? should we let the database fails?
            if (($fieldObject && is_array($columnValue) && (!$fieldObject->isJson() && !$fieldObject->isArray()))) {
                // $table = is_null($tableName) ? $this->table : $tableName;
                throw new SuppliedArrayAsColumnValue('Attempting to write an array as the value for column `' . $tableName . '`.`' . $columnName . '.');
            }
        }

        // @TODO: Dow we need to parse before insert?
        // Commented out because date are not saved correctly in GMT
        // $recordData = $this->parseRecord($recordData);

        $TableGateway = $this->makeTable($tableName);
        $primaryKey = $TableGateway->primaryKeyFieldName;
        $hasPrimaryKeyData = isset($recordData[$primaryKey]);
        $rowExists = false;

        if ($hasPrimaryKeyData) {
            $select = new Select($tableName);
            $select->columns([$primaryKey]);
            $select->where([
                $primaryKey => $recordData[$primaryKey]
            ]);
            $select->limit(1);
            $rowExists = $TableGateway->ignoreFilters()->selectWith($select)->count() > 0;
        }

        if ($rowExists) {
            $Update = new Update($tableName);
            $Update->set($recordData);
            $Update->where([
                $primaryKey => $recordData[$primaryKey]
            ]);
            $TableGateway->updateWith($Update);

            $this->runHook('postUpdate', [$TableGateway, $recordData, $this->adapter, null]);
        } else {
            $recordData = $this->applyHook('table.insert:before', $recordData, [
                'tableName' => $tableName
            ]);
            $recordData = $this->applyHook('table.insert.' . $tableName . ':before', $recordData);
            $TableGateway->insert($recordData);

            // Only get the last inserted id, if the column has auto increment value
            $columnObject = $this->getTableSchema()->getField($primaryKey);
            if ($columnObject->hasAutoIncrement()) {
                $recordData[$primaryKey] = $TableGateway->getLastInsertValue();
            }

            if ($tableName == 'directus_files' && static::$container) {
                $Files = static::$container->get('files');
                $ext = $thumbnailExt = pathinfo($recordData['filename'], PATHINFO_EXTENSION);

                // hotfix: pdf thumbnails are being saved to its original extension
                // file.pdf results into a thumbs/thumb.pdf instead of thumbs/thumb.jpeg
                if (Thumbnail::isNonImageFormatSupported($thumbnailExt)) {
                    $thumbnailExt = Thumbnail::defaultFormat();
                }

                $thumbnailPath = 'thumbs/THUMB_' . $recordData['filename'];
                if ($Files->exists($thumbnailPath)) {
                    $Files->rename($thumbnailPath, 'thumbs/' . $recordData[$this->primaryKeyFieldName] . '.' . $thumbnailExt);
                }

                $updateArray = [];
                if ($Files->getSettings('file_naming') == 'file_id') {
                    $Files->rename($recordData['filename'], str_pad($recordData[$this->primaryKeyFieldName], 11, '0', STR_PAD_LEFT) . '.' . $ext);
                    $updateArray['filename'] = str_pad($recordData[$this->primaryKeyFieldName], 11, '0', STR_PAD_LEFT) . '.' . $ext;
                    $recordData['filename'] = $updateArray['filename'];
                }

                if (!empty($updateArray)) {
                    $Update = new Update($tableName);
                    $Update->set($updateArray);
                    $Update->where([$TableGateway->primaryKeyFieldName => $recordData[$TableGateway->primaryKeyFieldName]]);
                    $TableGateway->updateWith($Update);
                }
            }

            $this->runHook('postInsert', [$TableGateway, $recordData, $this->adapter, null]);
        }

        $columns = TableSchema::getAllNonAliasTableColumnNames($tableName);
        $recordData = $TableGateway->fetchAll(function ($select) use ($recordData, $columns, $primaryKey) {
            $select
                ->columns($columns)
                ->limit(1);
            $select->where->equalTo($primaryKey, $recordData[$primaryKey]);
        })->current();

        return $recordData;
    }

    public function drop($tableName = null)
    {
        if ($tableName == null) {
            $tableName = $this->table;
        }

        if ($this->acl) {
            $this->acl->enforceAlter($tableName);
        }

        $dropped = false;
        if ($this->schemaManager->tableExists($tableName)) {
            // get drop table query
            $sql = new Sql($this->adapter);
            $drop = new Ddl\DropTable($tableName);
            $query = $sql->buildSqlString($drop);

            $this->runHook('table.drop:before', [$tableName]);

            $dropped = $this->getAdapter()->query(
                $query
            )->execute();

            $this->runHook('table.drop', [$tableName]);
            $this->runHook('table.drop:after', [$tableName]);
        }

        $this->stopManaging();

        return $dropped;
    }

    /**
     * Stop managing a table by removing privileges, preferences columns and table information
     *
     * @param null $tableName
     *
     * @return bool
     */
    public function stopManaging($tableName = null)
    {
        if ($tableName == null) {
            $tableName = $this->table;
        }

        // Remove table privileges
        if ($tableName != SchemaManager::TABLE_PERMISSIONS) {
            $privilegesTableGateway = new TableGateway(SchemaManager::TABLE_PERMISSIONS, $this->adapter);
            $privilegesTableGateway->delete(['collection' => $tableName]);
        }

        // Remove columns from directus_columns
        $columnsTableGateway = new TableGateway(SchemaManager::TABLE_FIELDS, $this->adapter);
        $columnsTableGateway->delete([
            'collection' => $tableName
        ]);

        // Remove table from directus_tables
        $tablesTableGateway = new TableGateway(SchemaManager::TABLE_COLLECTIONS, $this->adapter);
        $tablesTableGateway->delete([
            'collection' => $tableName
        ]);

        // Remove table from directus_collection_presets
        $preferencesTableGateway = new TableGateway(SchemaManager::TABLE_COLLECTION_PRESETS, $this->adapter);
        $preferencesTableGateway->delete([
            'collection' => $tableName
        ]);

        return true;
    }

    public function dropColumn($columnName, $tableName = null)
    {
        if ($tableName == null) {
            $tableName = $this->table;
        }

        if ($this->acl) {
            $this->acl->enforceAlter($tableName);
        }

        if (!TableSchema::hasTableColumn($tableName, $columnName, true)) {
            return false;
        }

        // Drop table column if is a non-alias column
        if (!array_key_exists($columnName, array_flip(TableSchema::getAllAliasTableColumns($tableName, true)))) {
            $sql = new Sql($this->adapter);
            $alterTable = new Ddl\AlterTable($tableName);
            $dropColumn = $alterTable->dropColumn($columnName);
            $query = $sql->getSqlStringForSqlObject($dropColumn);

            $this->adapter->query(
                $query
            )->execute();
        }

        // Remove column from directus_columns
        $columnsTableGateway = new TableGateway(SchemaManager::TABLE_FIELDS, $this->adapter);
        $columnsTableGateway->delete([
            'table_name' => $tableName,
            'column_name' => $columnName
        ]);

        // Remove column from sorting column in directus_preferences
        $preferencesTableGateway = new TableGateway(SchemaManager::TABLE_COLLECTION_PRESETS, $this->adapter);
        $preferencesTableGateway->update([
            'sort' => $this->primaryKeyFieldName,
            'sort_order' => 'ASC'
        ], [
            'table_name' => $tableName,
            'sort' => $columnName
        ]);

        return true;
    }

    /*
      Temporary solutions to fix add column error
        This add column is the same old-db add_column method
    */
    public function addColumn($tableName, $tableData)
    {
        // @TODO: enforce permission
        $directus_types = ['MANYTOMANY', 'ONETOMANY', 'ALIAS'];
        $relationshipType = ArrayUtils::get($tableData, 'relationship_type', null);
        // TODO: list all types which need manytoone ui
        // Hard-coded
        $manytoones = ['single_file', 'many_to_one', 'many_to_one_typeahead', 'MANYTOONE'];

        if (!in_array($relationshipType, $directus_types)) {
            $this->addTableColumn($tableName, $tableData);
            // Temporary solutions to #481, #645
            if (array_key_exists('ui', $tableData) && in_array($tableData['ui'], $manytoones)) {
                $tableData['relationship_type'] = 'MANYTOONE';
                $tableData['junction_key_right'] = $tableData['column_name'];
            }
        }

        //This is a 'virtual column'. Write to directus schema instead of MYSQL
        $this->addVirtualColumn($tableName, $tableData);

        return $tableData['column_name'];
    }

    // @TODO: TableGateway should not be handling table creation
    protected function addTableColumn($tableName, $columnData)
    {
        $column_name = $columnData['column_name'];
        $dataType = $columnData['data_type'];
        $comment = $this->getAdapter()->getPlatform()->quoteValue(ArrayUtils::get($columnData, 'comment', ''));

        if (array_key_exists('length', $columnData)) {
            $charLength = $columnData['length'];
            // SET and ENUM data type has its values in the char_length attribute
            // each value are separated by commas
            // it must be wrap into quotes
            if (!$this->schemaManager->isDecimalType($dataType) && strpos($charLength, ',') !== false) {
                $charLength = implode(',', array_map(function ($value) {
                    return '"' . trim($value) . '"';
                }, explode(',', $charLength)));
            }

            $dataType = $dataType . '(' . $charLength . ')';
        }

        $default = '';
        if (ArrayUtils::get($columnData, 'default_value')) {
            $value = ArrayUtils::get($columnData, 'default_value');
            $length = ArrayUtils::get($columnData, 'length');
            $defaultValue = $this->schemaManager->castDefaultValue($value, $dataType, $length);

            $default = ' DEFAULT ' . (is_string($defaultValue) ? sprintf('"%s"', $defaultValue) : $defaultValue);
        }

        // TODO: wrap this into an abstract DDL class
        $sql = 'ALTER TABLE `' . $tableName . '` ADD COLUMN `' . $column_name . '` ' . $dataType . $default . ' COMMENT "' . $comment . '"';

        $this->adapter->query($sql)->execute();
    }

    protected function addVirtualColumn($tableName, $columnData)
    {
        $alias_columns = ['table_name', 'column_name', 'data_type', 'related_table', 'junction_table', 'junction_key_left', 'junction_key_right', 'sort', 'ui', 'comment', 'relationship_type'];

        $columnData['table_name'] = $tableName;
        // NOTE: setting 9999 as default just because
        $columnData['sort'] = ArrayUtils::get($columnData, 'sort', 9999);

        $data = array_intersect_key($columnData, array_flip($alias_columns));
        return $this->addOrUpdateRecordByArray($data, 'directus_columns');
    }

    public function castFloatIfNumeric(&$value, $key)
    {
        if ($key != 'table_name') {
            $value = is_numeric($value) ? (float)$value : $value;
        }
    }

    /**
     * Convenience method for dumping a ZendDb Sql query object as debug output.
     *
     * @param  SqlInterface $query
     *
     * @return null
     */
    public function dumpSql(SqlInterface $query)
    {
        $sql = new Sql($this->adapter);
        $query = $sql->getSqlStringForSqlObject($query, $this->adapter->getPlatform());
        return $query;
    }

    public function ignoreFilters()
    {
        $this->options['filter'] = false;

        return $this;
    }

    /**
     * @param Select $select
     *
     * @return ResultSet
     *
     * @throws \Directus\Permissions\Exception\ForbiddenFieldReadException
     * @throws \Directus\Permissions\Exception\ForbiddenFieldWriteException
     * @throws \Exception
     */
    protected function executeSelect(Select $select)
    {
        $useFilter = ArrayUtils::get($this->options, 'filter', true) !== false;
        unset($this->options['filter']);

        if ($this->acl) {
            $this->enforceSelectPermission($select);
        }

        $selectState = $select->getRawState();
        $selectTableName = $selectState['table'];

        if ($useFilter) {
            $selectState = $this->applyHooks([
                'table.select:before',
                'table.select.' . $selectTableName . ':before',
            ], $selectState, [
                'tableName' => $selectTableName
            ]);

            // NOTE: This can be a "dangerous" hook, so for now we only support columns
            $select->columns(ArrayUtils::get($selectState, 'columns', ['*']));
        }

        try {
            $result = parent::executeSelect($select);
        } catch (UnexpectedValueException $e) {
            throw new InvalidQueryException(
                $this->dumpSql($select),
                $e
            );
        }

        if ($useFilter) {
            $result = $this->applyHooks([
                'table.select',
                'table.select.' . $selectTableName
            ], $result, [
                'selectState' => $selectState,
                'tableName' => $selectTableName
            ]);
        }

        return $result;
    }

    /**
     * @param Insert $insert
     *
     * @return mixed
     *
     * @throws \Directus\Database\Exception\InvalidQueryException
     */
    protected function executeInsert(Insert $insert)
    {
        if ($this->acl) {
            $this->enforceInsertPermission($insert);
        }

        $insertState = $insert->getRawState();
        $insertTable = $this->getRawTableNameFromQueryStateTable($insertState['table']);
        $insertData = $insertState['values'];
        // Data to be inserted with the column name as assoc key.
        $insertDataAssoc = array_combine($insertState['columns'], $insertData);

        $this->runHook('table.insert:before', [$insertTable, $insertDataAssoc]);
        $this->runHook('table.insert.' . $insertTable . ':before', [$insertDataAssoc]);

        try {
            $result = parent::executeInsert($insert);
        } catch (UnexpectedValueException $e) {
            throw new InvalidQueryException(
                $this->dumpSql($insert),
                $e
            );
        }

        $insertTableGateway = $this->makeTable($insertTable);

        // hotfix: directus_tables does not have auto generated value primary key
        if ($this->getTable() === SchemaManager::TABLE_COLLECTIONS) {
            $generatedValue = ArrayUtils::get($insertDataAssoc, $this->primaryKeyFieldName, 'table_name');
        } else {
            $generatedValue = $this->getLastInsertValue();
        }

        $resultData = $insertTableGateway->find($generatedValue);

        $this->runHook('table.insert', [$insertTable, $resultData]);
        $this->runHook('table.insert.' . $insertTable, [$resultData]);
        $this->runHook('table.insert:after', [$insertTable, $resultData]);
        $this->runHook('table.insert.' . $insertTable . ':after', [$resultData]);

        return $result;
    }

    /**
     * @param Update $update
     *
     * @return mixed
     *
     * @throws \Directus\Database\Exception\InvalidQueryException
     */
    protected function executeUpdate(Update $update)
    {
        $useFilter = ArrayUtils::get($this->options, 'filter', true) !== false;
        unset($this->options['filter']);

        if ($this->acl) {
            $this->enforceUpdatePermission($update);
        }

        $updateState = $update->getRawState();
        $updateTable = $this->getRawTableNameFromQueryStateTable($updateState['table']);
        $updateData = $updateState['set'];

        $isSoftDelete = $this->isSoftDelete($updateData);

        if ($useFilter) {
            $updateData = $this->runBeforeUpdateHooks($updateTable, $updateData, $isSoftDelete);
        }

        $update->set($updateData);

        try {
            $result = parent::executeUpdate($update);
        } catch (UnexpectedValueException $e) {
            throw new InvalidQueryException(
                $this->dumpSql($update),
                $e
            );
        }

        if ($useFilter) {
            $this->runAfterUpdateHooks($updateTable, $updateData, $isSoftDelete);
        }

        return $result;
    }

    /**
     * @param Delete $delete
     *
     * @return mixed
     *
     * @throws \Directus\Database\Exception\InvalidQueryException
     */
    protected function executeDelete(Delete $delete)
    {
        $ids = [];

        if ($this->acl) {
            $this->enforceDeletePermission($delete);
        }

        $deleteState = $delete->getRawState();
        $deleteTable = $this->getRawTableNameFromQueryStateTable($deleteState['table']);

        // Runs select PK with passed delete's $where before deleting, to use those for the even hook
        if ($pk = $this->primaryKeyFieldName) {
            $select = $this->sql->select();
            $select->where($deleteState['where']);
            $select->columns([$pk]);
            $results = parent::executeSelect($select);

            foreach($results as $result) {
                $ids[] = $result['id'];
            }
        }

        // skipping everything, if there is nothing to delete
        if ($ids) {
            $delete = $this->sql->delete();
            $expression = new In($pk, $ids);
            $delete->where($expression);

            // NOTE: this is used to send the "delete" data to table.remove hook
            // on update this hook pass the updated data array, on delete has nothing,
            // a empty array is passed instead
            $deleteData = $ids;

            foreach ($ids as $id) {
                $deleteData = ['id' => $id];
                $this->runHook('table.delete:before', [$deleteTable]);
                $this->runHook('table.delete.' . $deleteTable . ':before');
                $this->runHook('table.remove:before', [$deleteTable, $deleteData, 'soft' => false]);
                $this->runHook('table.remove.' . $deleteTable . ':before', [$deleteData, 'soft' => false]);
            }

            try {
                $result = parent::executeDelete($delete);
            } catch (UnexpectedValueException $e) {
                throw new InvalidQueryException(
                    $this->dumpSql($delete),
                    $e
                );
            }

            foreach ($ids as $id) {
                $deleteData = ['id' => $id];
                $this->runHook('table.delete', [$deleteTable, $deleteData]);
                $this->runHook('table.delete:after', [$deleteTable, $deleteData]);
                $this->runHook('table.delete.' . $deleteTable, [$deleteData]);
                $this->runHook('table.delete.' . $deleteTable . ':after', [$deleteData]);

                $this->runHook('table.remove', [$deleteTable, $deleteData, 'soft' => false]);
                $this->runHook('table.remove:after', [$deleteTable, $deleteData, 'soft' => false]);
                $this->runHook('table.remove.' . $deleteTable, [$deleteData, 'soft' => false]);
                $this->runHook('table.remove.' . $deleteTable . ':after', [$deleteData, 'soft' => false]);
            }

            return $result;
        }
    }

    /**
     * Check whether the data will perform soft delete
     *
     * @param array $data
     *
     * @return bool
     */
    protected function isSoftDelete(array $data)
    {
        $tableSchema = $this->getTableSchema();

        if (!$tableSchema->getStatusField()) {
            return false;
        }

        $deleteFlag = ArrayUtils::get($data, $this->deleteFlag);
        if ($deleteFlag === true) {
            return false;
        }

        $statusColumnName = $tableSchema->getStatusField()->getName();

        if (!ArrayUtils::has($data, $statusColumnName)) {
            return false;
        }

        $statusColumnObject = $tableSchema->getField($statusColumnName);
        $deletedValues = $this->getDeletedStatuses();
        $deletedValue = $statusColumnObject->getOptions('delete_value');
        if (!is_null($deletedValue)) {
            $deletedValues[] = $deletedValue;
        }

        $statusValue = ArrayUtils::get($data, $statusColumnName);
        $isSoftDelete =  in_array($statusValue, $this->getDeletedStatuses());

        return $isSoftDelete;
    }

    protected function getRawTableNameFromQueryStateTable($table)
    {
        if (is_string($table)) {
            return $table;
        }

        if (is_array($table)) {
            // The only value is the real table name (key is alias).
            return array_pop($table);
        }

        throw new \InvalidArgumentException('Unexpected parameter of type ' . get_class($table));
    }

    /**
     * Convert dates to ISO 8601 format
     *
     * @param array $records
     * @param Collection $tableSchema
     * @param null $tableName
     *
     * @return array|mixed
     */
    public function convertDates(array $records, Collection $tableSchema, $tableName = null)
    {
        $tableName = $tableName === null ? $this->table : $tableName;
        $isCustomTable = !$this->schemaManager->isDirectusTable($tableName);
        $hasSystemDateColumn = $this->schemaManager->hasSystemDateColumn($tableName);

        if (!$hasSystemDateColumn && $isCustomTable) {
            return $records;
        }

        // ==========================================================================
        // hotfix: records sometimes are no set as an array of rows.
        // NOTE: this code is duplicate @see: AbstractSchema::parseRecordValuesByType
        // ==========================================================================
        $singleRecord = false;
        if (!ArrayUtils::isNumericKeys($records)) {
            $records = [$records];
            $singleRecord = true;
        }

        foreach ($records as $index => $row) {
            foreach ($tableSchema->getFields() as $column) {
                $canConvert = in_array(strtolower($column->getType()), ['timestamp', 'datetime']);
                // Directus convert all dates to ISO to all datetime columns in the core tables
                // and any columns using system date interfaces (date_created or date_modified)
                if ($isCustomTable && !$column->isSystemDate()) {
                    $canConvert = false;
                }

                if ($canConvert) {
                    $columnName = $column->getId();
                    if (array_key_exists($columnName, $row)) {
                        $records[$index][$columnName] = DateUtils::convertToISOFormat($row[$columnName], 'UTC', get_user_timezone());
                    }
                }
            }
        }

        return $singleRecord ? reset($records) : $records;
    }

    /**
     * Parse records value by its column type
     *
     * @param array $records
     * @param null $tableName
     *
     * @return array
     */
    protected function parseRecordValuesByType(array $records, $tableName = null)
    {
        // NOTE: Performance spot
        $tableName = $tableName === null ? $this->table : $tableName;
        // Get the columns directly from the source
        // otherwise will keep in a circle loop loading Acl Instances
        $columns = TableSchema::getSchemaManagerInstance()->getFields($tableName);

        return $this->schemaManager->castRecordValues($records, $columns);
    }

    /**
     * Parse Records values (including format date by ISO 8601) by its column type
     *
     * @param $records
     * @param null $tableName
     *
     * @return array|mixed
     */
    public function parseRecord($records, $tableName = null)
    {
        // NOTE: Performance spot
        if (is_array($records)) {
            $tableName = $tableName === null ? $this->table : $tableName;
            $records = $this->parseRecordValuesByType($records, $tableName);
            $tableSchema = $this->getTableSchema($tableName);
            $records = $this->convertDates($records, $tableSchema, $tableName);
        }

        return $records;
    }

    /**
     * Enforce permission on Select
     *
     * @param Select $select
     *
     * @throws \Exception
     */
    protected function enforceSelectPermission(Select $select)
    {
        $selectState = $select->getRawState();
        $table = $this->getRawTableNameFromQueryStateTable($selectState['table']);

        // @TODO: enforce view permission

        // Enforce field read blacklist on Select's main table
        try {
            // @TODO: Enforce must return a list of columns without the blacklist
            // when asterisk (*) is used
            // and only throw and error when all the selected columns are blacklisted
            $this->acl->enforceReadField($table, $selectState['columns']);
        } catch (\Exception $e) {
            if ($selectState['columns'][0] != '*') {
                throw $e;
            }

            $selectState['columns'] = TableSchema::getAllNonAliasTableColumnsName($table);
            $this->acl->enforceReadField($table, $selectState['columns']);
        }

        // Enforce field read blacklist on Select's join tables
        foreach ($selectState['joins'] as $join) {
            $joinTable = $this->getRawTableNameFromQueryStateTable($join['name']);
            $this->acl->enforceReadField($joinTable, $join['columns']);
        }
    }

    /**
     * Enforce permission on Insert
     *
     * @param Insert $insert
     *
     * @throws \Exception
     */
    public function enforceInsertPermission(Insert $insert)
    {
        $insertState = $insert->getRawState();
        $insertTable = $this->getRawTableNameFromQueryStateTable($insertState['table']);

        $this->acl->enforceCreate($insertTable);

        // Enforce write field blacklist
        $this->acl->enforceWriteField($insertTable, $insertState['columns']);
    }

    /**
     * @param Builder $builder
     */
    protected function enforceReadPermission(Builder $builder)
    {
        // Make sure the user has permission to at least their items
        $this->acl->enforceRead($this->table);

        $userCreatedField = $this->getTableSchema()->getUserCreateField();

        // If there's not user created interface, user must have full read permission
        if (!$userCreatedField) {
            $this->acl->enforceReadAll($this->table);
            return;
        }

        // User can read all items, nothing else to check
        if ($this->acl->canReadAll($this->table)) {
            return;
        }

        $ownerIds = [$this->acl->getUserId()];
        if ($this->acl->canReadFromGroup($this->table)) {
            $ownerIds = array_merge(
                $ownerIds,
                get_user_ids_in_group($this->acl->getGroupId())
            );
        }

        $builder->whereIn($userCreatedField->getName(), $ownerIds);
    }

    /**
     * Enforce permission on Update
     *
     * @param Update $update
     *
     * @throws \Exception
     */
    public function enforceUpdatePermission(Update $update)
    {
        if ($this->acl->canUpdateAll($this->table)) {
            return;
        }

        $collectionObject = $this->getTableSchema();
        $currentUserId = $this->acl->getUserId();
        $currentGroupId = $this->acl->getGroupId();
        $updateState = $update->getRawState();
        $updateTable = $this->getRawTableNameFromQueryStateTable($updateState['table']);
        $select = $this->sql->select();
        $select->where($updateState['where']);
        $select->limit(1);
        $item = $this->ignoreFilters()->selectWith($select)->toArray();
        $item = reset($item);
        $statusId = null;

        // Item not found, item cannot be updated
        if (!$item) {
            throw new ForbiddenCollectionUpdateException($updateTable);
        }

        // Enforce write field blacklist
        $this->acl->enforceWriteField($updateTable, array_keys($updateState['set']));

        if ($collectionObject->hasStatusField()) {
            $statusField = $this->getTableSchema()->getStatusField();
            $statusId = $item[$statusField->getName()];
        }

        // User Created Interface not found, item cannot be updated
        $itemOwnerField = $this->getTableSchema()->getUserCreateField();
        if (!$itemOwnerField) {
            $this->acl->enforceUpdateAll($updateTable, $statusId);
            return;
        }

        // Owner not found, item cannot be updated
        $owner = get_item_owner($updateTable, $item[$collectionObject->getPrimaryKeyName()]);
        if (!is_array($owner)) {
            throw new ForbiddenCollectionUpdateException($updateTable);
        }

        $userItem = $currentUserId === $owner['id'];
        $groupItem = $currentGroupId === $owner['group'];
        if (!$userItem && !$groupItem && !$this->acl->canUpdateAll($updateTable, $statusId)) {
            throw new ForbiddenCollectionUpdateException($updateTable);
        }

        if (!$userItem && $groupItem) {
            $this->acl->enforceUpdateFromGroup($updateTable, $statusId);
        } else if ($userItem) {
            $this->acl->enforceUpdate($updateTable, $statusId);
        }
    }

    /**
     * Enforce permission on Delete
     *
     * @param Delete $delete
     *
     * @throws ForbiddenCollectionDeleteException
     */
    public function enforceDeletePermission(Delete $delete)
    {
        $collectionObject = $this->getTableSchema();
        $currentUserId = $this->acl->getUserId();
        $currentGroupId = $this->acl->getGroupId();
        $deleteState = $delete->getRawState();
        $deleteTable = $this->getRawTableNameFromQueryStateTable($deleteState['table']);
        // $cmsOwnerColumn = $this->acl->getCmsOwnerColumnByTable($deleteTable);
        // $canBigDelete = $this->acl->hasTablePrivilege($deleteTable, 'bigdelete');
        // $canDelete = $this->acl->hasTablePrivilege($deleteTable, 'delete');
        // $aclErrorPrefix = $this->acl->getErrorMessagePrefix();

        if (!$this->acl->canDelete($deleteTable)) {
            throw new ForbiddenCollectionDeleteException($deleteTable);
        }

        $select = $this->sql->select();
        $select->where($deleteState['where']);
        $select->limit(1);
        $item = $this->ignoreFilters()->selectWith($select)->toArray();
        $item = reset($item);
        $statusId = null;

        // Item not found, item cannot be updated
        if (!$item) {
            throw new ItemNotFoundException();
        }

        if ($collectionObject->hasStatusField()) {
            $statusField = $this->getTableSchema()->getStatusField();
            $statusId = $item[$statusField->getName()];
        }

        // User Created Interface not found, item cannot be updated
        $itemOwnerField = $this->getTableSchema()->getUserCreateField();
        if (!$itemOwnerField) {
            $this->acl->enforceDeleteAll($deleteTable, $statusId);
            return;
        }

        // Owner not found, item cannot be updated
        $owner = get_item_owner($deleteTable, $item[$collectionObject->getPrimaryKeyName()]);
        if (!is_array($owner)) {
            throw new ForbiddenCollectionDeleteException($deleteTable);
        }

        $userItem = $currentUserId === $owner['id'];
        $groupItem = $currentGroupId === $owner['group'];
        if (!$userItem && !$groupItem && !$this->acl->canDeleteAll($deleteTable, $statusId)) {
            throw new ForbiddenCollectionDeleteException($deleteTable);
        }

        if (!$userItem && $groupItem) {
            $this->acl->enforceDeleteFromGroup($deleteTable, $statusId);
        } else if ($userItem) {
            $this->acl->enforceDelete($deleteTable, $statusId);
        }

        // @todo: clean way
        // @TODO: this doesn't need to be bigdelete
        //        the user can only delete their own entry
        // if ($deleteTable === 'directus_bookmarks') {
        //     $canBigDelete = true;
        // }

        // @TODO: Update conditions
        // =============================================================================
        // Cannot delete if there's no magic owner column and can't big delete
        // All deletes are "big" deletes if there is no magic owner column.
        // =============================================================================
        // if (false === $cmsOwnerColumn && !$canBigDelete) {
        //     throw new ForbiddenCollectionDeleteException($aclErrorPrefix . 'The table `' . $deleteTable . '` is missing the `user_create_column` within `directus_collections` (BigHardDelete Permission Forbidden)');
        // } else if (!$canBigDelete) {
        //     // Who are the owners of these rows?
        //     list($predicateResultQty, $predicateOwnerIds) = $this->acl->getCmsOwnerIdsByTableGatewayAndPredicate($this, $deleteState['where']);
        //     if (!in_array($currentUserId, $predicateOwnerIds)) {
        //         //   $exceptionMessage = "Table harddelete access forbidden on $predicateResultQty `$deleteTable` table records owned by the authenticated CMS user (#$currentUserId).";
        //         $groupsTableGateway = $this->makeTable('directus_groups');
        //         $group = $groupsTableGateway->find($this->acl->getGroupId());
        //         $exceptionMessage = '[' . $group['name'] . '] permissions only allow you to [delete] your own items.';
        //         //   $aclErrorPrefix = $this->acl->getErrorMessagePrefix();
        //         throw new  ForbiddenCollectionDeleteException($exceptionMessage);
        //     }
        // }
    }

    /**
     * Get the column identifier with the specific quote and table prefixed
     *
     * @param string $column
     * @param string|null $table
     *
     * @return string
     */
    public function getColumnIdentifier($column, $table = null)
    {
        $platform = $this->getAdapter()->getPlatform();

        // TODO: find a common place to share this code
        // It is a duplicated code from Builder.php
        if (strpos($column, $platform->getIdentifierSeparator()) === false) {
            $column = implode($platform->getIdentifierSeparator(), [$table, $column]);
        }

        return $column;
    }

    /**
     * Get the column name from the identifier
     *
     * @param string $column
     *
     * @return string
     */
    public function getColumnFromIdentifier($column)
    {
        $platform = $this->getAdapter()->getPlatform();

        // TODO: find a common place to share this code
        // It is duplicated code in Builder.php
        if (strpos($column, $platform->getIdentifierSeparator()) !== false) {
            $identifierParts = explode($platform->getIdentifierSeparator(), $column);
            $column = array_pop($identifierParts);
        }

        return $column;
    }

    /**
     * Get the table name from the identifier
     *
     * @param string $column
     * @param string|null $table
     *
     * @return string
     */
    public function getTableFromIdentifier($column, $table = null)
    {
        $platform = $this->getAdapter()->getPlatform();

        if ($table === null) {
            $table = $this->getTable();
        }

        // TODO: find a common place to share this code
        // It is duplicated code in Builder.php
        if (strpos($column, $platform->getIdentifierSeparator()) !== false) {
            $identifierParts = explode($platform->getIdentifierSeparator(), $column);
            $table = array_shift($identifierParts);
        }

        return $table;
    }

    /**
     * Gets schema manager
     *
     * @return SchemaManager|null
     */
    public function getSchemaManager()
    {
        return $this->schemaManager;
    }

    /**
     * Set application container
     *
     * @param $container
     */
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    /**
     * @return Container
     */
    public static function getContainer()
    {
        return static::$container;
    }

    public static function setHookEmitter($emitter)
    {
        static::$emitter = $emitter;
    }

    public function runHook($name, $args = null)
    {
        if (static::$emitter) {
            static::$emitter->execute($name, $args);
        }
    }

    /**
     * Apply a list of hook against the given data
     *
     * @param array $names
     * @param null $data
     * @param array $attributes
     *
     * @return array|\ArrayObject|null
     */
    public function applyHooks(array $names, $data = null, array $attributes = [])
    {
        foreach ($names as $name) {
            $data = $this->applyHook($name, $data, $attributes);
        }

        return $data;
    }

    /**
     * Apply hook against the given data
     *
     * @param $name
     * @param null $data
     * @param array $attributes
     *
     * @return \ArrayObject|array|null
     */
    public function applyHook($name, $data = null, array $attributes = [])
    {
        // TODO: Ability to run multiple hook names
        // $this->applyHook('hook1,hook2');
        // $this->applyHook(['hook1', 'hook2']);
        // ----------------------------------------------------------------------------
        // TODO: Move this to a separate class to handle common events
        // $this->applyNewRecord($table, $record);
        if (static::$emitter && static::$emitter->hasFilterListeners($name)) {
            $isResultSet = $data instanceof ResultSetInterface;
            $resultSet = null;

            if ($isResultSet) {
                $resultSet = $data;
                $data = $resultSet->toArray();
            }

            $data = static::$emitter->apply($name, $data, $attributes);

            if ($isResultSet && $resultSet) {
                $data = new \ArrayObject($data);
                $resultSet->initialize($data->getIterator());
                $data = $resultSet;
            }
        }

        return $data;
    }

    /**
     * Run before table update hooks and filters
     *
     * @param string $updateTable
     * @param array $updateData
     * @param bool $isSoftDelete
     *
     * @return array|\ArrayObject
     */
    protected function runBeforeUpdateHooks($updateTable, $updateData, $isSoftDelete)
    {
        // Filters
        $updateData = $this->applyHook('table.update:before', $updateData, [
            'tableName' => $updateTable
        ]);
        $updateData = $this->applyHook('table.update.' . $updateTable . ':before', $updateData);

        // Hooks
        $this->runHook('table.update:before', [$updateTable, $updateData]);
        $this->runHook('table.update.' . $updateTable . ':before', [$updateData]);

        if ($isSoftDelete === true) {
            $updateData = $this->applyHook('table.remove:before', $updateData, [
                'tableName' => $updateTable,
                'soft' => true
            ]);

            $updateData = $this->applyHook('table.remove.' . $updateTable . ':before', $updateData, [
                'soft' => true
            ]);

            $this->runHook('table.remove:before', [$updateTable, $updateData, 'soft' => true]);
            $this->runHook('table.remove.' . $updateTable . ':before', [$updateData, 'soft' => true]);
        }

        return $updateData;
    }

    /**
     * Run after table update hooks and filters
     *
     * @param string $updateTable
     * @param string $updateData
     * @param bool $isSoftDelete
     */
    protected function runAfterUpdateHooks($updateTable, $updateData, $isSoftDelete)
    {
        $this->runHook('table.update', [$updateTable, $updateData]);
        $this->runHook('table.update:after', [$updateTable, $updateData]);
        $this->runHook('table.update.' . $updateTable, [$updateData]);
        $this->runHook('table.update.' . $updateTable . ':after', [$updateData]);

        if ($isSoftDelete === true) {
            $this->runHook('table.remove', [$updateTable, $updateData, 'soft' => true]);
            $this->runHook('table.remove.' . $updateTable, [$updateData, 'soft' => true]);
            $this->runHook('table.remove:after', [$updateTable, $updateData, 'soft' => true]);
            $this->runHook('table.remove.' . $updateTable . ':after', [$updateData, 'soft' => true]);
        }
    }

    /**
     * Gets Directus settings (from DB)
     *
     * @param null $key
     *
     * @return mixed
     */
    public function getSettings($key = null)
    {
        $settings = [];

        if (static::$container) {
            $settings = static::$container->get('app.settings');
        }

        return $key !== null ? ArrayUtils::get($settings, $key) : $settings;
    }

    public function getDeletedValue()
    {
        $statusColumnName = $this->getStatusColumnName();
        $deletedValue = null;

        if ($statusColumnName) {
            $statusColumnObject = $this->getTableSchema()->getField($statusColumnName);
            $deletedValue = ArrayUtils::get($statusColumnObject->getOptions(), 'delete_value');
        }

        return $deletedValue;
    }

    /**
     * Get the table statuses
     *
     * @return array
     */
    public function getAllStatuses()
    {
        $statuses = [];

        if (static::$container && TableSchema::hasStatusColumn($this->table, true)) {
            /** @var Config $config */
            $config = static::$container->get('config');
            $statusMapping = $this->getTableSchema()->getStatusMapping() ?: [];
            $statuses = $config->getAllStatusesValue($statusMapping);
        }

        return $statuses;
    }

    /**
     * Gets the table published statuses
     *
     * @return array
     */
    public function getPublishedStatuses()
    {
        return $this->getStatuses('published');
    }

    /**
     * Get the table deleted statuses
     *
     * @return array
     */
    public function getDeletedStatuses()
    {
        return $this->getStatuses('deleted');
    }

    /**
     * Gets the table statuses with the given type
     *
     * @param $type
     *
     * @return array
     */
    protected function getStatuses($type)
    {
        $statuses = [];

        if (static::$container && TableSchema::hasStatusColumn($this->table, true)) {
            /** @var Config $config */
            $config = static::$container->get('config');
            $statusMapping = $this->getTableSchema()->getStatusMapping() ?: [];

            switch ($type) {
                case 'published':
                    $statuses = $config->getPublishedStatusesValue($statusMapping);
                    break;
                case 'deleted':
                    $statuses = $config->getDeletedStatusesValue($statusMapping);
                    break;
            }
        }

        return $statuses;
    }
}
