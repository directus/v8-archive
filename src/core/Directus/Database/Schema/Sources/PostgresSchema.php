<?php
namespace Directus;

namespace Directus\Database\Schema\Sources;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Directus\Util\ArrayUtils;
use Zend\Db\Sql\Predicate\In;
use Directus\Util\StringUtils;
use Zend\Db\Sql\Ddl\AlterTable;
use Zend\Db\Sql\Ddl\CreateTable;
use Zend\Db\Sql\TableIdentifier;
use Directus\Exception\Exception;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Ddl\Column\Column;
use Directus\Database\Schema\DataTypes;
use function Directus\get_directus_setting;
use function Directus\compact_sort_to_array;
use Directus\Database\Schema\Sources\AbstractSchema;
use Directus\Database\Schema\Sources\Query\PostgresBuilder;
use Directus\Database\Schema\Sources\Expression\PostgresChangeColumn;
use Directus\Database\Schema\Sources\Expression\PostgresAddIntegerColumn;

class PostgresSchema extends AbstractSchema
{

    /**
     * Database connection adapter
     *
     * @var \Zend\DB\Adapter\Adapter
     */
    protected $adapter;

    /**
     * AbstractSchema constructor.
     *
     * @param $adapter
     */
    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get the schema name
     *
     * @return string
     */
    public function getSchemaName()
    {
        return $this->adapter->getCurrentSchema();
    }

    /**
     * @return \Zend\DB\Adapter\Adapter
     */
    public function getConnection()
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getCollections(array $params = [])
    {
        $select = new Select();
        $select->columns([
            'collection' => 'table_name',
            /** does not exist in postgresql */
            'date_created' => null/*'CREATE_TIME'*/,
            //'collation' => null/*TABLE_COLLATE*/,
            //'schema_comment' => null/*'TABLE_COMMENT'*/
        ]);
        $select->from(['ST' => new TableIdentifier('tables', 'information_schema')]);
        $select->join(
            ['DT' => 'directus_collections'],
            'DT.collection = ST.table_name',
            [
                'note',
                'hidden' => new Expression('COALESCE("DT"."hidden", false)'),
                'single' => new Expression('COALESCE("DT"."single", false)'),
                'managed' => new Expression('CASE WHEN "DT"."collection" IS NULL THEN false ELSE "DT"."managed" END')
            ],
            $select::JOIN_LEFT
        );
        $select->join(
            ['PGD' => 'pg_database'],
            'PGD.datname = ST.table_schema',
            [
                'collation' => 'datcollate'
            ],
            $select::JOIN_LEFT
        );
        $select->join(
            ['PGE' => 'pg_description'],
            'PGE.objoid = PGD.oid',
            [
                'schema_comment' => 'description'
            ],
            $select::JOIN_LEFT
        );

        $condition = [
            'ST.table_schema' => $this->adapter->getCurrentSchema(),
            'ST.table_type' => 'BASE TABLE'
        ];

        $select->where($condition);
        if (isset($params['name'])) {
            $tableName = $params['name'];
            // hotfix: This solve the problem fetching a table with capital letter
            $where = $select->where->nest();
            $where->equalTo('ST.table_name', $tableName);
            $where->OR;
            $where->equalTo('ST.table_name', $tableName);
            $where->unnest();
        }

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function collectionExists($collectionsName)
    {
        if (is_string($collectionsName)) {
            $collectionsName = [$collectionsName];
        }

        $select = new Select();
        $select->columns(['table_name']);
        $select->from(['T' => new TableIdentifier('tables', 'information_schema')]);
        $select->where([
            new In('T.table_name', $collectionsName),
            'T.table_schema' => $this->adapter->getCurrentSchema()
        ]);

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->count() ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function getCollection($collectionName)
    {
        return $this->getCollections(['name' => $collectionName]);
    }

    /**
     * @inheritDoc
     */
    public function getFields($tableName, array $params = [])
    {
        return $this->getAllFields(array_merge($params, ['collection' => $tableName]));
    }

    public function getDefaultLengths()
    {
        return [
            'CHARACTER VARYING' => 255,
            'VARCHAR' => 255
        ];
    }

    /**
     * @inheritDoc
     */
    public function getAllFields(array $params = [])
    {
        $selectOne = new Select();
        // $selectOne->quantifier($selectOne::QUANTIFIER_DISTINCT);
        $selectOne->columns([
            'collection' => 'table_name',
            'field' => 'column_name',
            'datatype' => new Expression('UPPER("SF".data_type)'),
            'key' => new Expression('"TC".constraint_type'),
            'unique' => new Expression('CASE WHEN "TC".constraint_type=\'UNIQUE\' THEN true ELSE false END'),
            'primary_key' => new Expression('CASE WHEN "TC".constraint_type=\'PRIMARY KEY\' THEN true ELSE false END'),
            /** auto_increment can be emulated with an identity, a serial or a sequence in postgresql */
            'auto_increment' => new Expression('CASE WHEN "SF".is_identity=\'YES\' THEN true WHEN "SF".data_type ~* \'.*(serial).*\' THEN true WHEN "SF".column_default LIKE \'nextval%\' THEN true ELSE false END'),
            /** does not exist in postgresql */
            'extra' => null/*'extra'*/,
            'char_length' => 'character_maximum_length',
            'precision' => 'numeric_precision',
            'scale' => 'numeric_scale',
            'nullable' => new Expression('CASE WHEN "SF".is_nullable=\'YES\' THEN true ELSE false END'),
            'default_value' =>  new Expression('(SELECT "SF".column_default)'),
            /** Getting the column comment is too expensive */
            //'note' => new Expression('COALESCE("DF".note, "SF".column_comment)'),
            'note' => new Expression('"DF".note'),
            'column_type' => 'data_type',
            'signed' => new Expression('CASE WHEN "SF".data_type ~* \'.*(smallint)|(integer)|(bigint)|(decimal)|(numeric)|(real)|(double).*\' THEN true ELSE false END'),
        ]);

        $selectOne->from(['SF' => new TableIdentifier('columns', 'information_schema')]);
        $selectOne->join(
            ['KCU' => new TableIdentifier('key_column_usage', 'information_schema')],
            'SF.table_schema = KCU.constraint_schema AND SF.table_name = KCU.table_name and SF.column_name = KCU.column_name',
            [
                /*'table_schema',
                'table_name',
                'constraint_name'*/
            ],
            $selectOne::JOIN_LEFT
        );
        $selectOne->join(
            ['TC' => new TableIdentifier('table_constraints', 'information_schema')],
            'KCU.table_schema = TC.constraint_schema AND KCU.table_name = TC.table_name and KCU.constraint_name = TC.constraint_name',
            [
                /*'constraint_type',
                'key' => 'constraint_type'*/
            ],
            $selectOne::JOIN_LEFT
        );
        $selectOne->join(
            ['DF' => 'directus_fields'],
            'SF.column_name = DF.field AND SF.table_name = DF.collection',
            [
                'id',
                'type',
                'sort',
                'managed' =>  new Expression('CASE WHEN "DF".id IS NULL THEN false ELSE true END'),
                'interface',
                'hidden_detail',
                'hidden_browse',
                'required',
                'options',
                'locked',
                'translation',
                'readonly',
                'width',
                'validation',
                'group'
            ],
            $selectOne::JOIN_LEFT
        );

        $selectOne->where([
            'SF.table_schema' => $this->adapter->getCurrentSchema(),
            // 'T.table_type' => 'BASE TABLE'
        ]);

        if (isset($params['collection'])) {
            $selectOne->where([
                'SF.table_name' => $params['collection']
            ]);
        }

        if (isset($params['field'])) {
            $where = $selectOne->where->nest();
            $where->equalTo('DF.field', $params['field']);
            $where->or;
            $where->equalTo('SF.column_name', $params['field']);
            $where->unnest();
        }

        $selectOne->order(['collection' => 'ASC', 'SF.ordinal_position' => 'ASC']);

        $selectTwo = new Select();
        $selectTwo->columns([
            'collection',
            'field',
            'datatype' => new Expression('NULL'),
            'key' => new Expression('NULL'),
            'unique' => new Expression('NULL'),
            'primary_key' => new Expression('NULL'),
            'auto_increment' => new Expression('NULL'),
            'extra' => new Expression('NULL'),
            'char_length' => new Expression('NULL'),
            'precision' => new Expression('NULL'),
            'scale' => new Expression('NULL'),
            'is_nullable' => new Expression('\'NO\''),
            'default_value' => new Expression('NULL'),
            'note',
            'column_type' => new Expression('NULL'),
            'signed' => new Expression('NULL'),
            'id',
            'type' => new Expression('UPPER(type)'),
            'sort',
            'managed' =>  new Expression('CASE WHEN "DF2".id IS NULL THEN false ELSE true END'),
            'interface',
            'hidden_detail',
            'hidden_browse',
            'required',
            'options',
            'locked',
            'translation',
            'readonly',
            'width',
            'validation',
            'group',
        ]);
        $selectTwo->from(['DF2' => 'directus_fields']);

        $where = new Where();
        $where->addPredicate(new In(new Expression('LOWER(type)'), DataTypes::getAliasTypes()));
        if (isset($params['collection'])) {
            $where->equalTo('DF2.collection', $params['collection']);
        }

        $selectTwo->where($where);

        $selectOne->combine($selectTwo);

        $sorts = ArrayUtils::get($params, 'sort', 'collection');
        if (is_string($sorts)) {
            $sorts = StringUtils::csv($sorts);
        }

        $sql = new Sql($this->adapter);
        $selectUnion = new Select();
        $selectUnion->from(['fields' => $selectOne]);

        $sortNullLast = (bool) get_directus_setting('sort_null_last', true);
        foreach ($sorts as $field) {
            $sort = compact_sort_to_array($field);
            if ($sortNullLast) {
                $selectUnion->order(new IsNull(key($sort)));
            }

            $selectUnion->order($sort);
        }

        if (ArrayUtils::has($params, 'limit')) {
            $selectUnion->limit((int) ArrayUtils::get($params, 'limit'));
        }

        $statement = $sql->prepareStatementForSqlObject($selectUnion);
        /*$s = $statement->getSql();
        $d = $statement->getParameterContainer()->getNamedArray();
        $logger = new \Monolog\Logger('PGSQL_DUMP');
        $formatter = new \Monolog\Formatter\LineFormatter();
        $formatter->allowInlineLineBreaks();
        $handler = new \Monolog\Handler\StreamHandler('d:/sql_dump.log');
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        $logger->error($s, $d);*/
        $result = $statement->execute();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function hasField($tableName, $columnName)
    {
        // TODO: Implement hasColumn() method.
    }

    /**
     * @inheritDoc
     */
    public function getField($tableName, $columnName)
    {
        return $this->getFields($tableName, ['field' => $columnName])->current();
    }

    /**
     * @inheritdoc
     */
    public function getAllRelations()
    {
        // TODO: Implement getAllRelations() method.
    }

    public function getRelations($collectionName)
    {
        $selectOne = new Select();
        $selectOne->columns([
            'id',
            'collection_many',
            'field_many',
            'collection_one',
            'field_one'
        ]);

        $selectOne->from('directus_relations');

        $where = $selectOne->where->nest();
        $where->equalTo('collection_many', $collectionName);
        $where->OR;
        $where->equalTo('collection_one', $collectionName);
        $where->unnest();

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($selectOne);
        $result = $statement->execute();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function hasPrimaryKey($tableName)
    {
        // TODO: Implement hasPrimaryKey() method.
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryKey($tableName)
    {
        $select = new Select();
        $columnName = null;

        // @todo: make this part of loadSchema
        // without the need to use acl and create a infinite nested function call
        $select->columns([
            'column_name' => new Expression('"SF".column_name')
        ]);
        $select->from(['SF' => new TableIdentifier('columns', 'information_schema')]);
        $select->join(
            ['KCU' => new TableIdentifier('key_column_usage', 'information_schema')],
            'SF.table_schema = KCU.constraint_schema AND SF.table_name = KCU.table_name and SF.column_name = KCU.column_name',
            [],
            $select::JOIN_INNER
        );
        $select->join(
            ['TC' => new TableIdentifier('table_constraints', 'information_schema')],
            'KCU.table_schema = TC.constraint_schema AND KCU.table_name = TC.table_name and KCU.constraint_name = TC.constraint_name',
            [],
            $select::JOIN_INNER
        );
        $select->where([
            '"SF".table_name' => $tableName,
            '"SF".table_schema' => $this->adapter->getCurrentSchema(),
            '"TC".constraint_type' => 'PRIMARY KEY'
        ]);

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        // @TODO: Primary key can be more than one.
        $column = $result->current();
        if ($column) {
            $columnName = $column['column_name'];
        }

        return $columnName;
    }

    /**
     * @inheritDoc
     */
    public function getFullSchema()
    {
        // TODO: Implement getFullSchema() method.
    }

    /**
     * @inheritDoc
     */
    public function getColumnUI($column)
    {
        // TODO: Implement getColumnUI() method.
    }

    /**
     * Add primary key to an existing column
     *
     * @param $table
     * @param $column
     *
     * @return \Zend\Db\Adapter\Driver\StatementInterface|\Zend\Db\ResultSet\ResultSet
     *
     * @throws Exception
     */
    public function addPrimaryKey($table, $column)
    {
        $columnData = $this->getField($table, $column);

        if (!$columnData) {
            // TODO: Better error message
            throw new Exception('Missing column');
        }

        $dataType = ArrayUtils::get($columnData, 'type');

        if (!$dataType) {
            // TODO: Better error message
            throw new Exception('Missing data type');
        }

        $queryFormat = 'ALTER TABLE "%s" ADD PRIMARY KEY("%s")';
        // NOTE: Make this work with strings
        if ($this->isIntegerType($dataType)) {
            $queryFormat .= ', ALTER COLUMN "%s" ADD GENERATED BY DEFAULT AS IDENTITY';
        }

        $query = sprintf($queryFormat, $table, $column, $column);
        $connection = $this->adapter;

        return $connection->query($query, $connection::QUERY_MODE_EXECUTE);
    }

    /**
     * @inheritDoc
     */
    public function dropPrimaryKey($table, $column)
    {
        $columnData = $this->getField($table, $column);

        if (!$columnData) {
            // TODO: Better message
            throw new Exception('Missing column');
        }

        $dataType = ArrayUtils::get($columnData, 'type');

        if (!$dataType) {
            // TODO: Better message
            throw new Exception('Missing data type');
        }

        $queryFormat = 'ALTER TABLE "%s" ALTER COLUMN "%s" DROP CONSTRAINT \'%s_pkey\', DROP DEFAULT';
        $query = sprintf($queryFormat, $table, $column, $column);
        $connection = $this->adapter;

        return $connection->query($query, $connection::QUERY_MODE_EXECUTE);
    }

    /**
     * Cast string values to its database type.
     *
     * @param $data
     * @param $type
     * @param $length
     *
     * @return mixed
     */
    public function castValue($data, $type = null, $length = false)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'boolean':
            case 'bool': //alias
                $data = boolval($data);
                break;
            case 'json':
            case 'jsonb':
                if ($data) {
                    $data = is_string($data) ? json_decode($data) : $data;
                } else {
                    $data = null;
                }
                break;
            case 'bytea':
                // NOTE: Do we really need to encode the blob?
                $data = base64_encode($data);
                break;
            case 'smallserial':
            case 'serial2': //alias
            case 'serial':
            case 'serial4': //alias
            case 'smallint':
            case 'int2': //alias
            case 'integer':
            case 'int4': //alias
            case 'int': //alias
            // do not cast bigint values. php doesn't support bigint
            // case 'bigint':
            // case 'bigserial':
            // Only cast if the value is numeric already
            // Avoid casting when the hooks already have cast numeric data type set as boolean type
                if (is_numeric($data)) {
                    $data = (int) $data;
                }
                break;
            case 'numeric':
            case 'decimal': //alias
            case 'double precision':
            case 'float8': //alias
            case 'real':
            case 'float4': //alias
            case 'money':
            case 'float':
                $data = (float)$data;
                break;
            case 'date':
            case 'timestamp':
            case 'timestamp without time zone':
                $format = 'Y-m-d';
                $zeroData = '0000-00-00';
                if ($type !== 'date') {
                    $format .= ' H:i:s';
                    $zeroData .= ' 00:00:00';
                }

                if ($data === $zeroData) {
                    $data = null;
                }
                $datetime = \DateTime::createFromFormat($format, $data);
                $data = $datetime ? $datetime->format($format) : null;
                break;
            case 'time':
                // NOTE: Assuming this are all valid formatted data
                $data = !empty($data) ? $data : null;
                break;
            case 'character':
            case 'char': //alias
            case 'character varying':
            case 'varchar': //alias
            case 'text':
                break;
        }

        return $data;
    }

    public function parseType($data, $type = null, $length = false)
    {
        return $this->castValue($data, $type, $length);
    }

    /**
     * @inheritdoc
     */
    public function getFloatingPointTypes()
    {
        return [
            'double precision',
            'float8', //alias
            'real',
            'float4', //alias
            'money',
            'numeric',
            'decimal', //alias
            'float'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIntegerTypes()
    {
        return [
            'smallserial',
            'serial',
            'smallint',
            'integer',
            'bigserial',
            'bigint',
            'serial2', //alias
            'serial4', //alias
            'int2', //alias
            'int4', //alias
            'int' //alias
        ];
    }

    /**
     * @inheritdoc
     */
    public function getNumericTypes()
    {
        return array_merge($this->getFloatingPointTypes(), $this->getIntegerTypes());
    }

    /**
     * @inheritdoc
     */
    public function isFloatingPointType($type)
    {
        return $this->isType($type, $this->getFloatingPointTypes());
    }

    /**
     * @inheritdoc
     */
    public function isIntegerType($type)
    {
        return $this->isType($type, $this->getIntegerTypes());
    }

    /**
     * @inheritdoc
     */
    public function isNumericType($type)
    {
        return in_array(strtolower($type), $this->getNumericTypes());
    }

    /**
     * @inheritdoc
     */
    public function getStringTypes()
    {
        return [
            'character',
            'char', //alias
            'character varying',
            'varchar', //alias
            'text'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isStringType($type)
    {
        return in_array(strtolower($type), $this->getStringTypes());
    }

    /**
     * @inheritdoc
     */
    public function getDateAndTimeTypes()
    {
        return [
            'date',
            'time',
            'timestamp',
            'timestamp without time zone'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isDateAndTimeTypes($type)
    {
        return in_array(strtolower($type), $this->getDateAndTimeTypes());
    }

    /**
     * @inheritdoc
     */
    public function getTypesRequireLength()
    {
        return [
            'character varying',
            'varchar', //alias
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTypesAllowLength()
    {
        return [
            'character',
            'char', //alias
            'numeric',
            'decimal', //alias
            'float'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isTypeLengthRequired($type)
    {
        return in_array(strtolower($type), $this->getTypesRequireLength());
    }

    /**
     * @inheritdoc
     */
    public function isTypeLengthAllowed($type)
    {
        return in_array(strtolower($type), $this->getTypesAllowLength());
    }

    /**
     * Get the last generated value for this field, if it's based on a sequence or auto-incremented
     * Do not use along with SequenceFeature
     *
     * @return boolean
     */
    public function getLastGeneratedId($abstractTableGateway, $table, $field)
    {
        $sql = "SELECT CURRVAL(pg_get_serial_sequence('" . $table . "', '" . $field . "'))";

        $statement = $abstractTableGateway->getAdapter()->createStatement();
        $statement->prepare($sql);
        $result = $statement->execute();
        $sequence = $result->current();
        unset($statement, $result);
        return $sequence['currval'];
    }

    public function getNextGeneratedId($abstractTableGateway, $table, $field)
    {
        $sql = "SELECT NEXTVAL(pg_get_serial_sequence('" . $table . "', '" . $field . "'))";

        $statement = $abstractTableGateway->getAdapter()->createStatement();
        $statement->prepare($sql);
        $result = $statement->execute();
        $sequence = $result->current();
        unset($statement, $result);
        return $sequence['nextval'];
    }

    /**
     * Creates column based on type
     *
     * @param $name
     * @param $type
     *
     * @return Column
     *
     * @throws UnknownTypeException
     */
    public function createColumnFromDataType($name, $type)
    {
        // TODO: Move this to the Schema Source
        switch (strtolower($type)) {
            case 'character':
            case 'char':
                $column = new \Zend\Db\Sql\Ddl\Column\Char($name);
                break;
            case 'character varying':
            case 'varchar':
                $column = new \Zend\Db\Sql\Ddl\Column\Varchar($name);
                break;
            case 'text':
                $column = new \Zend\Db\Sql\Ddl\Column\Text($name);
                break;
            case 'time':
                $column = new \Zend\Db\Sql\Ddl\Column\Time($name);
                break;
            case 'date':
                $column = new \Zend\Db\Sql\Ddl\Column\Date($name);
                break;
            case 'timestamp':
            case 'timestamp without time zone':
                $column = new \Zend\Db\Sql\Ddl\Column\Timestamp($name);
                break;
            case 'bool':
            case 'boolean':
                $column = new \Zend\Db\Sql\Ddl\Column\Boolean($name);
                break;
            case 'smallserial':
            case 'serial2': //alias
            case 'serial':
            case 'serial4': //alias                
            case 'smallint':
            case 'int2': //alias
            case 'integer':
            case 'int4': //alias
            case 'int': //alias
            case 'bigint':
            case 'bigserial':
                $column = new \Zend\Db\Sql\Ddl\Column\Integer($name);
                break;
            case 'float':
                $column = new \Zend\Db\Sql\Ddl\Column\Floating($name);
                break;
            case 'money':
            case 'double precision':
            case 'float8': //alias
                $column = new \Directus\Database\Ddl\Column\Double($name);
                break;
            case 'numeric':
            case 'decimal': //alias
                $column = new \Directus\Database\Ddl\Column\Numeric($name);
                break;
            case 'real':
            case 'float4': //alias
                $column = new \Directus\Database\Ddl\Column\Real($name);
                break;
            case 'bytea':
                $column = new \Zend\Db\Sql\Ddl\Column\Binary($name);
                break;
            default:
                $column = new \Directus\Database\Ddl\Column\Custom($type, $name);
                break;
        }

        return $column;
    }

    /**
     * Fix defaultZendDB choices if applicable
     *
     * @param AbstractSql|AlterTable|CreateTable|DropTable $table
     * @param Sql $sql
     *
     * @return String
     */
    public function buildSql($table, $sql)
    {
        if ($table instanceof AlterTable) {
            //Zend-Db Specifications are protected :(

            //ALTER TABLE for PotgreSQL doesn't work with a single CHANGE COLUMN
            //but multiple calls to ALTER COLUMN for each piece of data to change
            $sealBreaker = function () {
                $this->specifications[AlterTable::CHANGE_COLUMNS] = [
                            "%1\$s" => [
                                [2 => "%2\$s,\n", 'combinedby' => ""]
                            ]
                        ];
                foreach ($this->changeColumns as $key => $value) {
                    if ($value instanceof Column) {
                        //decorate
                        $this->changeColumns[$key] = new PostgresChangeColumn($value);
                    }
                }
                return $this;
            };
            $sealBreaker->call($table);
        } else if ($table instanceof CreateTable) {
            //Zend-DB doesn't handle IDENTITY data types
            $sealBreaker = function () {
                foreach ($this->columns as $key => $value) {
                    if ($value instanceof \Zend\Db\Sql\Ddl\Column\Integer) {
                        //decorate
                        $this->columns[$key] = new PostgresAddIntegerColumn($value);
                    }
                }
                return $this;
            };
            $sealBreaker->call($table);
        }
        return parent::buildSql($table, $sql);
    }

    /**
     * Transform if needed the default value of a field
     * @param array $field
     * 
     * @return array $field
     */
    public function transformField(array $field)
    {        
        $transformedField = parent::transformField($field);
        if ($transformedField['default_value']) {

            //Do not output default values for auto-incremented fields; it shouldn't be manually reset by users anyway
            if ($field['auto_increment']==true) {
                $transformedField['default_value'] = null;
            } else {

                //Simplify how default values are written for simple cases
                //Drop explicit PostgreSQL typecasting info if redundant
                $transformedField['default_value'] = preg_replace('/(.+)::'.$transformedField['datatype'].'$/i', '${1}', $transformedField['default_value']);

                if ($this->isStringType($transformedField['datatype'])) {
                    // When the field datatype is string we should only make sure to remove the first and last character
                    // Those characters are the quote wrapping the default value
                    // As a default string can have quotes as default (defined by the user) We should avoid to remove those
                    $quoteSymbol = $this->adapter->getPlatform()->getQuoteValueSymbol();
                    if ($transformedField['default_value'][0]==$quoteSymbol) {
                        $transformedField['default_value'] = substr($transformedField['default_value'], 1, -1);
                        $transformedField['default_value'] = str_replace($quoteSymbol.$quoteSymbol, $quoteSymbol, $transformedField['default_value']);
                    }
                } elseif ($this->isDateAndTimeTypes($transformedField['datatype'])) {
                    // All date types shouldn't have any quotes
                    // Trim all quotes should be safe as the database doesn't support invalidate values
                    // Unless it's a function such as `current_timestamp()` which again shouldn't have quotes
                    $transformedField['default_value'] = trim($transformedField['default_value'], '\'');
                }
            }
        }
        return $transformedField;
    }

    /**
     * get a new SQL Builder
     * @param AdapterInterface $adapter
     * 
     * @return Builder
     */
    public function getBuilder($adapter)
    {
        return new PostgresBuilder($adapter);
    }
}
