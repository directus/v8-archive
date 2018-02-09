<?php

namespace Directus\Database\Schema\Sources;

use Directus\Exception\Exception;
use Directus\Util\ArrayUtils;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\IsNotNull;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Where;

class MySQLSchema extends AbstractSchema
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
            'collection' => 'TABLE_NAME',
            'date_created' => 'CREATE_TIME',
            'collation' => 'TABLE_COLLATION',
            'schema_comment' => 'TABLE_COMMENT'
        ]);
        $select->from(['ST' => new TableIdentifier('TABLES', 'INFORMATION_SCHEMA')]);
        $select->join(
            ['DT' => 'directus_collections'],
            'DT.collection = ST.TABLE_NAME',
            [
                'comment',
                'hidden' => new Expression('IFNULL(`DT`.`hidden`, 0)'),
                'single' => new Expression('IFNULL(`DT`.`single`, 0)'),
                'item_name_template',
                'preview_url',
                'status_mapping',
                'managed' => new Expression('IF(ISNULL(`DT`.`collection`), 0, 1)')
            ],
            $select::JOIN_LEFT
        );

        $condition = [
            'ST.TABLE_SCHEMA' => $this->adapter->getCurrentSchema(),
            'ST.TABLE_TYPE' => 'BASE TABLE'
        ];

        $select->where($condition);
        if (isset($params['name'])) {
            $tableName = $params['name'];
            // hotfix: This solve the problem fetching a table with capital letter
            $where = $select->where->nest();
            $where->equalTo('ST.TABLE_NAME', $tableName);
            $where->OR;
            $where->equalTo('ST.TABLE_NAME', $tableName);
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
        $select->columns(['TABLE_NAME']);
        $select->from(['T' => new TableIdentifier('TABLES', 'INFORMATION_SCHEMA')]);
        $select->where([
            new In('T.TABLE_NAME', $collectionsName),
            'T.TABLE_SCHEMA' => $this->adapter->getCurrentSchema()
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
    public function getFields($tableName, $params = null)
    {
        return $this->getAllFields(['collection' => $tableName]);
        $selectOne = new Select();
        $selectOne->quantifier($selectOne::QUANTIFIER_DISTINCT);
        $selectOne->columns([
            'table_name' => 'TABLE_NAME',
            'column_name' => 'COLUMN_NAME',
            'type' => new Expression('UCASE(C.DATA_TYPE)'),
            'key' => 'COLUMN_KEY',
            'extra' => 'EXTRA',
            'char_length' => 'CHARACTER_MAXIMUM_LENGTH',
            'precision' => 'NUMERIC_PRECISION',
            'scale' => 'NUMERIC_SCALE',
            'is_nullable' => 'IS_NULLABLE',
            'default_value' => 'COLUMN_DEFAULT',
            'comment' => new Expression('IFNULL(comment, COLUMN_COMMENT)'),
            'sort' => new Expression('IFNULL(sort, ORDINAL_POSITION)'),
            'column_type' => 'COLUMN_TYPE',

            'interface' => new Expression('D.interface'),
            'hidden_input' => new Expression('IFNULL(D.hidden_input, 0)'),
            'required' => new Expression('IFNULL(D.required, 0)'),
            'options' => new Expression('D.options'),

            'relationship_type' => new Expression('NULL'),
            'collection_a' => new Expression('NULL'),
            'store_key_a' => new Expression('NULL'),
            'store_collection' => new Expression('NULL'),
            'collection_b' => new Expression('NULL'),
            'store_key_b' => new Expression('NULL'),
        ]);
        $selectOne->from(['C' => new TableIdentifier('COLUMNS', 'INFORMATION_SCHEMA')]);
        $selectOne->join(
            ['D' => 'directus_fields'],
            'C.COLUMN_NAME = D.field AND C.TABLE_NAME = D.collection',
            [
                // 'interface',
                // 'hidden_input' => new Expression('IFNULL(hidden_input, 0)'),
                // 'required' => new Expression('IFNULL(D.required, 0)'),
                // 'options',
            ],
            $selectOne::JOIN_LEFT
        );

        $selectOne->join(
            ['T' => new TableIdentifier('TABLES', 'INFORMATION_SCHEMA')],
            'C.TABLE_NAME = T.TABLE_NAME',
            [],//['TABLE_NAME' => 'TABLE_NAME'],
            $selectOne::JOIN_LEFT
        );

        $where = new Where();
        $where
            ->equalTo('C.TABLE_SCHEMA', $this->adapter->getCurrentSchema())
            ->equalTo('T.TABLE_TYPE', 'BASE TABLE')
            ->equalTo('C.TABLE_NAME', $tableName);

        $selectOne->where($where);

        $selectTwo = new Select();
        $selectTwo->columns([
            'id' => 'field',
            'collection',
            'field',
            'type' => new Expression('UCASE(type)'),
            'key' => new Expression('NULL'),
            'extra' => new Expression('NULL'),
            'char_length' => new Expression('NULL'),
            'precision' => new Expression('NULL'),
            'scale' => new Expression('NULL'),
            'is_nullable' => new Expression('"NO"'),
            'default_value' => new Expression('NULL'),
            'comment',
            'sort',
            'column_type' => new Expression('NULL'),

            'interface',
            'hidden_input',
            'required' => new Expression('IFNULL(required, 0)'),
            'options',

            'relationship_type' => new Expression('R.relationship_type'),
            'collection_a' => new Expression('R.collection_a'),
            'store_key_a' => new Expression('R.store_key_a'),
            'store_collection' => new Expression('R.store_collection'),
            'collection_b' => new Expression('R.collection_b'),
            'store_key_b' => new Expression('R.store_key_b'),

        ]);
        $selectTwo->from(['F' => 'directus_fields']);

        $selectTwo->join(
            ['R' => 'directus_relations'],
            'F.collection = R.collection_a AND F.field = R.field_a',
            [
                // 'relationship_type',
                // 'collection_a',
                // 'store_key_a',
                // 'store_collection',
                // 'collection_b',
                // 'store_key_b'
            ],
            $selectOne::JOIN_RIGHT
        );
        $where = new Where();
        $where
            ->equalTo('collection', $tableName)
            ->addPredicate(new In('type', ['ALIAS', 'MANYTOMANY', 'ONETOMANY']));
            // ->nest()
            // // NOTE: is this actually necessary?
            // ->addPredicate(new \Zend\Db\Sql\Predicate\Expression('"' . $columnName . '" = -1'))
            // ->OR
            // ->equalTo('field', $columnName)
            // ->unnest()
            // ->addPredicate(new IsNotNull('relationship_type'));

        if (isset($params['blacklist']) && count($params['blacklist']) > 0) {
            $where->addPredicate(new NotIn('field', $params['blacklist']));
        }

        $selectTwo->where($where);
        $selectTwo->order('sort');

        $selectOne->combine($selectTwo);

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($selectOne);
        $result = $statement->execute();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getAllFields(array $params = [])
    {
        $selectOne = new Select();
        // $selectOne->quantifier($selectOne::QUANTIFIER_DISTINCT);
        $selectOne->columns([
            'collection' => 'TABLE_NAME',
            'field' => 'COLUMN_NAME',
            'sort' => new Expression('IFNULL(DF.sort, SF.ORDINAL_POSITION)'),
            'type' => new Expression('UCASE(SF.DATA_TYPE)'),
            'key' => 'COLUMN_KEY',
            'extra' => 'EXTRA',
            'char_length' => 'CHARACTER_MAXIMUM_LENGTH',
            'precision' => 'NUMERIC_PRECISION',
            'scale' => 'NUMERIC_SCALE',
            'nullable' => new Expression('IF(SF.IS_NULLABLE="YES",1,0)'),
            'default_value' => 'COLUMN_DEFAULT',
            'comment' => new Expression('IFNULL(DF.comment, SF.COLUMN_COMMENT)'),
            'column_type' => 'COLUMN_TYPE',
        ]);

        $selectOne->from(['SF' => new TableIdentifier('COLUMNS', 'INFORMATION_SCHEMA')]);
        $selectOne->join(
            ['DF' => 'directus_fields'],
            'SF.COLUMN_NAME = DF.field AND SF.TABLE_NAME = DF.collection',
            [
                'interface',
                'hidden_input' => new Expression('IF(DF.hidden_input=1,1,0)'),
                'required' => new Expression('IF(DF.required=1,1,0)'),
                'options'
            ],
            $selectOne::JOIN_LEFT
        );

        $selectOne->where([
            'SF.TABLE_SCHEMA' => $this->adapter->getCurrentSchema(),
            // 'T.TABLE_TYPE' => 'BASE TABLE'
        ]);

        if (isset($params['collection'])) {
            $selectOne->where([
                'SF.TABLE_NAME' => $params['collection']
            ]);
        }

        $selectTwo = new Select();
        $selectTwo->columns([
            'collection',
            'field',
            'sort',
            'type' => new Expression('UCASE(type)'),
            'key' => new Expression('NULL'),
            'extra' => new Expression('NULL'),
            'char_length' => new Expression('NULL'),
            'precision' => new Expression('NULL'),
            'scale' => new Expression('NULL'),
            'is_nullable' => new Expression('"NO"'),
            'default_value' => new Expression('NULL'),
            'comment',
            'column_type' => new Expression('NULL'),
            'interface',
            'hidden_input',
            'required',
            'options',
        ]);
        $selectTwo->from(['DF2' => 'directus_fields']);

        $where = new Where();
        $where->addPredicate(new In(new Expression('UCASE(type)'), ['ALIAS']));
        if (isset($params['collection'])) {
            $where->equalTo('DF2.collection', $params['collection']);
        }

        $selectTwo->where($where);

        $selectOne->combine($selectTwo);//, $selectOne::COMBINE_UNION, 'ALL');
        $selectOne->order('collection');

        $sql = new Sql($this->adapter);
        $statement = $sql->prepareStatementForSqlObject($selectOne);
        $result = $statement->execute();

        // echo $sql->buildSqlString($selectOne);exit;

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
        return $this->getColumns($tableName, ['field' => $columnName])->current();
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
        // $selectOne->quantifier($selectOne::QUANTIFIER_DISTINCT);
        $selectOne->columns([
            'id',
            'collection_a',
            'field_a',
            'junction_key_a',
            'junction_collection',
            'junction_mixed_collections',
            'junction_key_b',
            'collection_b',
            'field_b'
        ]);

        $selectOne->from('directus_relations');

        $where = $selectOne->where->nest();
        $where->equalTo('collection_a', $collectionName);
        $where->OR;
        $where->equalTo('collection_b', $collectionName);
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
            'column_name' => 'COLUMN_NAME'
        ]);
        $select->from(new TableIdentifier('COLUMNS', 'INFORMATION_SCHEMA'));
        $select->where([
            'TABLE_NAME' => $tableName,
            'TABLE_SCHEMA' => $this->adapter->getCurrentSchema(),
            'COLUMN_KEY' => 'PRI'
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
        $columnData = $this->getColumn($table, $column);

        if (!$columnData) {
            // TODO: Better error message
            throw new Exception('Missing column');
        }

        $dataType = ArrayUtils::get($columnData, 'type');

        if (!$dataType) {
            // TODO: Better error message
            throw new Exception('Missing data type');
        }

        $queryFormat = 'ALTER TABLE `%s` ADD PRIMARY KEY(`%s`)';
        // NOTE: Make this work with strings
        if ($this->isNumericType($dataType)) {
            $queryFormat .= ', MODIFY COLUMN `%s` %s AUTO_INCREMENT';
        }

        $query = sprintf($queryFormat, $table, $column, $column, $dataType);
        $connection = $this->adapter;

        return $connection->query($query, $connection::QUERY_MODE_EXECUTE);
    }

    /**
     * @inheritDoc
     */
    public function dropPrimaryKey($table, $column)
    {
        $columnData = $this->getColumn($table, $column);

        if (!$columnData) {
            // TODO: Better message
            throw new Exception('Missing column');
        }

        $dataType = ArrayUtils::get($columnData, 'type');

        if (!$dataType) {
            // TODO: Better message
            throw new Exception('Missing data type');
        }

        $queryFormat = 'ALTER TABLE `%s` CHANGE COLUMN `%s` `%s` %s NOT NULL, DROP PRIMARY KEY';
        $query = sprintf($queryFormat, $table, $column, $column, $dataType);
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
            case 'blob':
            case 'mediumblob':
                // NOTE: Do we really need to encode the blob?
                $data = base64_encode($data);
                break;
            case 'year':
            case 'bigint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'long':
            case 'tinyint':
                $data = ($data === null) ? null : (int)$data;
                break;
            case 'float':
                $data = (float)$data;
                break;
            case 'date':
            case 'datetime':
                $format = 'Y-m-d';
                $zeroData = '0000-00-00';
                if ($type === 'datetime') {
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
            case 'char':
            case 'varchar':
            case 'text':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
            case 'var_string':
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
    public function getDecimalTypes()
    {
        return [
            'double',
            'decimal',
            'float'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getIntegerTypes()
    {
        return [
            'year',
            'bigint',
            'smallint',
            'mediumint',
            'int',
            'long',
            'tinyint'
        ];
    }

    /**
     * @inheritdoc
     */
    public function getNumericTypes()
    {
        return array_merge($this->getDecimalTypes(), $this->getIntegerTypes());
    }

    /**
     * @inheritdoc
     */
    public function isDecimalType($type)
    {
        return $this->isType($type, $this->getDecimalTypes());
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
            'char',
            'varchar',
            'text',
            'enum',
            'set',
            'tinytext',
            'text',
            'mediumtext',
            'longtext'
        ];
    }

    /**
     * @inheritdoc
     */
    public function isStringType($type)
    {
        return in_array(strtolower($type), $this->getStringTypes());
    }
}
