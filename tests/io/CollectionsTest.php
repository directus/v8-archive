<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\CollectionNotFoundException;
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
            'collection' => static::$tableName,
            'fields' => [
                [
                    'field' => 'id',
                    'type' => 'primary_key',
                    'datatype' => 'integer',
                    'interface' => 'primary_key',
                    'primary_key' => true,
                    'auto_increment' => true,
                    'signed' => false,
                    'length' => 11,
                ],
                [
                    'field' => 'status',
                    'type' => 'integer',
                    'interface' => 'status',
                    'signed' => false,
                    'length' => 11,
                ],
                [
                    'field' => 'sort',
                    'type' => 'integer',
                    'interface' => 'sort',
                    'signed' => false,
                    'length' => 11,
                ],
                [
                    'field' => 'name',
                    'interface' => 'text_input',
                    'type' => 'VARCHAR',
                    'length' => 255
                ]
            ]
        ];

        $response = request_post('collections', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ArrayUtils::pick($data, 'collection'));

        $this->assertTrue(table_exists(static::$db, static::$tableName));

        // Has Directus tables record
        $result = table_find(static::$db, 'directus_collections', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 1);

        // Has columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 4);
    }

    public function testGetOne()
    {
        $response = request_get('collections/' . static::$tableName, $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => static::$tableName
        ]);

        $response = request_error_get('collections/nonexisting', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => CollectionNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }

    public function testUpdate()
    {
        $data = [
            'hidden' => true,
            'single' => true
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge([
            'collection' => static::$tableName
        ], $data));

        // Change back
        $data = [
            'hidden' => false,
            'single' => false
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge([
            'collection' => static::$tableName
        ], $data));

        // Columns: Add one, Update one
        $data = [
            'fields' => [
                [
                    'field' => 'name',
                    'length' => 64
                ],
                [
                    'field' => 'datetime',
                    'type' => 'datetime',
                    'interface' => 'datetime',
                    'required' => true
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => static::$tableName
        ]);

        // Has the new columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 5);

        // =============================================================================
        // Columns: Add one
        // =============================================================================
        $data = [
            'fields' => [
                [
                    'field' => 'datetime_two',
                    'type' => 'datetime',
                    'interface' => 'datetime',
                    'required' => true
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => static::$tableName
        ]);

        // Has the new columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 6);

        // =============================================================================
        // Columns: Update one
        // =============================================================================
        $data = [
            'fields' => [
                [
                    'field' => 'datetime_two',
                    'required' => false
                ]
            ]
        ];

        $response = request_patch('collections/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => static::$tableName
        ]);

        // didn't add new columns information
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 6);
    }

    public function testList()
    {
        $response = request_get('collections', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 19 // 15 core tables, categories, products, products_images and static::$tableName
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('collections/' . static::$tableName, ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('collections/' . static::$tableName, $this->queryParams);
        assert_response_error($this, $response, [
            'code' => CollectionNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(table_exists(static::$db, static::$tableName));

        // Empty collections records
        $result = table_find(static::$db, 'directus_collections', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 0);

        // empty fields records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertTrue(count($result) === 0);
    }
}
