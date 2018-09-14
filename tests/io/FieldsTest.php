<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;
use Directus\Database\Exception\FieldNotFoundException;
use Directus\Database\Exception\UnknownDataTypeException;
use Directus\Database\Schema\DataTypes;
use Directus\Exception\UnprocessableEntityException;

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
                    'type' => 'primary_key',
                    'datatype' => 'integer',
                    'length' => 11,
                    'primary_key' => true,
                    'auto_increment' => true,
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

    public function testCreateAliasFields()
    {
        $fieldsCount = 2;
        $types = DataTypes::getAliasTypes();
        foreach ($types as $type) {
            $data = [
                'field' => $type,
                'interface' => 'text_input',
                'type' => $type
            ];

            $response = request_post('fields/' . static::$tableName, $data, ['query' => $this->queryParams]);
            assert_response($this, $response);
            assert_response_data_contains($this, $response, $data);

            // Has columns records
            $result = table_find(static::$db, 'directus_fields', [
                'collection' => static::$tableName
            ]);

            $fieldsCount++;
            $this->assertTrue(count($result) === $fieldsCount);
            $this->assertFalse(column_exists(static::$db, static::$tableName, $type));
        }

        // ----------------------------------------------------------------------------
        // change type to another alias type
        // ----------------------------------------------------------------------------
        $typeIdx = count($types);
        foreach ($types as $type) {
            $typeIdx--;
            $data = [
                'type' => $types[$typeIdx]
            ];

            $path = sprintf('fields/%s/%s', static::$tableName, $type);
            $response = request_patch($path, $data, ['query' => $this->queryParams]);
            assert_response($this, $response);
            assert_response_data_contains($this, $response, $data);

            // Has columns records
            $result = table_find(static::$db, 'directus_fields', [
                'collection' => static::$tableName
            ]);

            $this->assertTrue(count($result) === $fieldsCount);
            $this->assertFalse(column_exists(static::$db, static::$tableName, $type));
        }

        // ----------------------------------------------------------------------------
        // change alias type to a non-alias type
        // ----------------------------------------------------------------------------
        foreach ($types as $type) {
            $data = [
                'type' => 'integer',
                'length' => 10
            ];

            $path = sprintf('fields/%s/%s', static::$tableName, $type);
            $response = request_patch($path, $data, ['query' => $this->queryParams]);
            assert_response($this, $response);
            assert_response_data_contains($this, $response, $data, false);

            // Has columns records
            $result = table_find(static::$db, 'directus_fields', [
                'collection' => static::$tableName
            ]);

            $this->assertTrue(count($result) === $fieldsCount);
            $this->assertTrue(column_exists(static::$db, static::$tableName, $type));
        }

        // ----------------------------------------------------------------------------
        // change non-alias type to an alias type
        // ----------------------------------------------------------------------------
        foreach ($types as $type) {
            $data = [
                'type' => $type
            ];

            $path = sprintf('fields/%s/%s', static::$tableName, $type);
            $response = request_patch($path, $data, ['query' => $this->queryParams]);
            assert_response($this, $response);
            assert_response_data_contains($this, $response, $data);

            // Has columns records
            $result = table_find(static::$db, 'directus_fields', [
                'collection' => static::$tableName
            ]);

            $this->assertTrue(count($result) === $fieldsCount);
            $this->assertFalse(column_exists(static::$db, static::$tableName, $type));
        }

        foreach ($types as $type) {
            $response = request_delete('fields/' . static::$tableName . '/' . $type, ['query' => $this->queryParams]);
            assert_response_empty($this, $response);
        }
    }

    public function testCreateUnknownDataType()
    {
        // Create a test table
        $data = [
            'collection' => 'unknown_type',
            'fields' => [
                [
                    'field' => 'id',
                    'datatype' => 'integer',
                    'type' => 'primary_key',
                    'length' => 10,
                    'primary_key' => true,
                    'auto_increment' => true,
                    'interface' => 'primary_key'
                ],
                [
                    'field' => 'test',
                    'type' => 'unknown',
                    'interface' => 'unknown'
                ]
            ]
        ];

        $response = request_error_post('collections', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => UnknownDataTypeException::ERROR_CODE,
            'status' => 422
        ]);
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
            'type' => 'varchar',
            'interface' => 'text_input'
        ]);

        // Has columns records
        $result = table_find(static::$db, 'directus_fields', [
            'collection' => static::$tableName
        ]);
        $this->assertSame(2, count($result));
        $this->assertTrue(column_exists(static::$db, static::$tableName, 'name'));
    }

    public function testUpdateUnknownDataType()
    {
        $data = [
            'type' => 'unknown',
            'length' => 255,
        ];

        $response = request_error_patch('fields/' . static::$tableName . '/name', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => UnknownDataTypeException::ERROR_CODE,
            'status' => 422
        ]);
    }

    public function testGetOne()
    {
        $data = [
            'field' => 'name',
            'interface' => 'text_input',
            'collection' => 'test'
        ];

        $response = request_get('fields/' . static::$tableName . '/name', $this->queryParams);
        assert_response($this, $response, [
            'datatype',
            'default_value',
            'auto_increment',
            'primary_key',
            'unique',
            'signed',
            'length',
        ]);
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
            'options' => $options
        ];

        // Using form_params convert true/false to "1"/"0"
        $response = request_patch('fields/' . static::$tableName . '/name', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'options' => json_decode(json_encode(['option' => 1, 'read_only' => 0]))
        ], false);

        $data = [
            'options' => $options
        ];

        $response = request_patch('fields/' . static::$tableName . '/name', $data, ['json' => true, 'query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'options' => json_decode(json_encode($options))
        ], false);
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
            'code' => FieldNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $response = request_error_delete('fields/' . static::$tableName . '/id', ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => UnprocessableEntityException::ERROR_CODE,
            'status' => 422
        ]);
    }
}
