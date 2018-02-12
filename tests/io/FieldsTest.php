<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;
use Directus\Database\Exception\ColumnNotFoundException;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Exception\BadRequestException;
use Directus\Exception\ErrorException;
use Directus\Util\ArrayUtils;

class FieldsTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    /**
     * @var Connection
     */
    protected static $db;

    /**
     * @var string
     */
    protected static $tableName = 'test';

    public static function resetDatabase()
    {
        reset_table_id(static::$db, 'directus_fields', 5);
        delete_item(static::$db, 'directus_collections', [
            'collection' => static::$tableName
        ]);
        drop_table(static::$db, static::$tableName);
    }

    public static function setUpBeforeClass()
    {
        static::$db = create_db_connection();
        static::resetDatabase();
    }

    public static function tearDownAfterClass()
    {
        static::resetDatabase();
    }

    public function testCreate()
    {
        // Create a test table
        $data = [
            'collection' => static::$tableName,
            'fields' => [
                [
                    'field' => 'id',
                    'type' => 'integer',
                    'interface' => 'primary_key'
                ]
            ]
        ];

        $response = request_post('collections', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => static::$tableName
        ]);

        // --------------------------------------------------------------------
        $data = [
            'field' => 'name',
            'interface' => 'text_input',
            'length' => 100,
            'type' => 'varchar'
        ];

        $response = request_post('fields/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'field' => 'name',
            'interface' => 'text_input',
            'type' => 'varchar'
        ]);

        // Has columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 2);
        $this->assertTrue(column_exists(static::$db, static::$tableName, 'name'));
    }

    public function testUpdate()
    {
        $data = [
            'field' => 'name',
            'length' => 255,
        ];

        $response = request_patch('fields/' . static::$tableName . '/name', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'field' => 'name',
            'type' => 'VARCHAR',
            'interface' => 'text_input'
        ]);

        // Has columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertSame(2, count($result));
        $this->assertTrue(column_exists(static::$db, static::$tableName, 'name'));
    }

    public function testGetOne()
    {
        $data = [
            'field' => 'name',
            'interface' => 'text_input',
            'collection' => 'test'
        ];

        $response = request_get('fields/' . static::$tableName . '/name', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
        $this->assertTrue(column_exists(static::$db, static::$tableName, 'name'));
    }

    public function testUpdateOptions()
    {
        $options = [
            'option' => true,
            'read_only' => false
        ];

        $data = [
            'options' => json_encode($options)
        ];

        $response = request_patch('fields/' . static::$tableName . '/name', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'options' => json_encode($options)
        ]);

        $data = [
            'options' => $options
        ];

        $response = request_patch('fields/' . static::$tableName . '/name', $data, ['json' => true, 'query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'options' => json_encode($options)
        ]);
    }

    public function testGetOptions()
    {
        $response = request_get('fields/' . static::$tableName . '/name', $this->queryParams);
        assert_response($this, $response, [
            'has_fields' => true,
            'fields' => ['options']
        ]);
    }

    public function testList()
    {
        $response = request_get('fields/' . static::$tableName, $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('fields/' . static::$tableName . '/name', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('fields/'. static::$tableName . '/name', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ColumnNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $response = request_error_delete('fields/' . static::$tableName . '/id', ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => BadRequestException::ERROR_CODE,
            'status' => 400
        ]);
    }
}
