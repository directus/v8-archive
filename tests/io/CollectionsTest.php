<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;
use Directus\Util\ArrayUtils;

class CollectionsTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    protected static $tableName = 'items';
    protected static $db;

    public static function resetDatabase()
    {
        drop_table(static::$db, static::$tableName);
        delete_item(static::$db, 'directus_tables', [
            'table_name' => static::$tableName
        ]);
        reset_table_id('directus_columns', 7);
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
        $data = [
            'table_name' => static::$tableName,
            'columns' => [
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'interface' => 'primary_key',
                    'auto_increment' => true,
                    'unsigned' => true
                ],
                [
                    'name' => 'status',
                    'type' => 'integer',
                    'interface' => 'status',
                    'unsigned' => true
                ],
                [
                    'name' => 'sort',
                    'type' => 'integer',
                    'interface' => 'sort',
                    'unsigned' => true
                ],
                [
                    'name' => 'name',
                    'interface' => 'text_input',
                    'type' => 'VARCHAR',
                    'length' => 255
                ]
            ]
        ];

        $response = request_post('collections', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ArrayUtils::pick($data, 'table_name'));

        $this->assertTrue(table_exists(static::$db, static::$tableName));

        // Has Directus tables record
        $result = table_find(static::$db, 'directus_tables', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 1);

        // Has columns records
        $result = table_find(static::$db, 'directus_columns', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 4);
    }

    public function testGetOne()
    {
        $response = request_get('collections/' . static::$tableName, $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'table_name' => static::$tableName
        ]);

        $response = request_error_get('collections/nonexisting', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }

    public function testUpdate()
    {
        $data = [
            'hidden' => 1,
            'single' => 1
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge([
            'table_name' => static::$tableName
        ], $data));

        // Change back
        $data = [
            'hidden' => 0,
            'single' => 0
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge([
            'table_name' => static::$tableName
        ], $data));

        // Columns: Add one, Update one
        $data = [
            'columns' => [
                [
                    'name' => 'name',
                    'length' => 64
                ],
                [
                    'name' => 'datetime',
                    'type' => 'datetime',
                    'interface' => 'datetime',
                    'required' => true
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'table_name' => static::$tableName
        ]);

        // Has the new columns records
        $result = table_find(static::$db, 'directus_columns', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 5);

        // =============================================================================
        // Columns: Add one
        // =============================================================================
        $data = [
            'columns' => [
                [
                    'name' => 'datetime_two',
                    'type' => 'datetime',
                    'interface' => 'datetime',
                    'required' => true
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'table_name' => static::$tableName
        ]);

        // Has the new columns records
        $result = table_find(static::$db, 'directus_columns', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 6);

        // =============================================================================
        // Columns: Update one
        // =============================================================================
        $data = [
            'columns' => [
                [
                    'name' => 'datetime_two',
                    'required' => false
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'table_name' => static::$tableName
        ]);

        // didn't add new columns information
        $result = table_find(static::$db, 'directus_columns', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 6);
    }

    public function testList()
    {
        $response = request_get('collections', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 7
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('collections/' . static::$tableName, ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('collections/' . static::$tableName, $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(table_exists(static::$db, static::$tableName));

        // Empty collections records
        $result = table_find(static::$db, 'directus_tables', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 0);

        // empty fields records
        $result = table_find(static::$db, 'directus_columns', [
            'table_name' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 0);
    }
}
