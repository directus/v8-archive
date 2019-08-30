<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;
use Directus\Database\Exception\FieldNotFoundException;
use Directus\Database\Exception\UnknownTypeException;
use Directus\Database\Schema\DataTypes;
use Directus\Exception\UnprocessableEntityException;
use Directus\Util\ArrayUtils;
use Directus\Util\DateTimeUtils;

class FieldsTest extends \PHPUnit\Framework\TestCase
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
    protected static $testDateTime = 'datetime_test';

    public static function resetDatabase()
    {
        drop_table(static::$db, static::$tableName);
        drop_table(static::$db, static::$testDateTime);
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
                    'datatype' => 'integer',
                    'primary_key' => true,
                    'auto_increment' => true,
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
            'length' => 100,
            'type' => 'string',
            'datatype' => 'varchar',
        ];

        $response = request_post('fields/' . static::$tableName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'field' => 'name',
            'type' => 'string',
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
                'datatype' => 'integer',
            ];

            $path = sprintf('fields/%s/%s', static::$tableName, $type);
            $response = request_patch($path, $data, ['query' => $this->queryParams]);
            assert_response($this, $response);
            assert_response_data_contains($this, $response, ArrayUtils::omit($data, 'datatype'), false);

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
                    'primary_key' => true,
                    'auto_increment' => true,
                ],
                [
                    'field' => 'test',
                    'type' => 'unknown',
                ]
            ]
        ];

        $response = request_error_post('collections', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => UnknownTypeException::ERROR_CODE,
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
            'type' => 'string',
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
            'datatype' => 'varchar',
            'length' => 255,
        ];

        $response = request_error_patch('fields/' . static::$tableName . '/name', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'code' => UnknownTypeException::ERROR_CODE,
            'status' => 422
        ]);
    }

    public function testGetOne()
    {
        $data = [
            'field' => 'name',
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

    public function testDateTimeTypes()
    {
        $collectionName = static::$testDateTime;

        // Create a test table
        $data = [
            'collection' => $collectionName,
            'fields' => [
                [
                    'field' => 'id',
                    'type' => 'integer',
                    'datatype' => 'integer',
                    'primary_key' => true,
                    'auto_increment' => true,
                ],
                [
                    'field' => 'datetime',
                    'type' => 'datetime',
                    'datatype' => 'datetime',
                ],
                [
                    'field' => 'date',
                    'type' => 'date',
                    'datatype' => 'date',
                ],
                [
                    'field' => 'time',
                    'type' => 'time',
                    'datatype' => 'time',
                ],
            ]
        ];

        $response = request_post('collections', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'collection' => $collectionName
        ]);

        // Using ISO format
        $datetime = new DateTimeUtils();
        $isoDateTime = $datetime->toISO8601Format();
        $data = [
            'datetime' => $isoDateTime,
            'date' => $isoDateTime,
            'time' => $isoDateTime,
        ];

        $response = request_post('items/' . $collectionName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'datetime' => $datetime->toUTC()->toISO8601Format(),
            'date' => $datetime->toString($datetime::DEFAULT_DATE_FORMAT),
            'time' => $datetime->toString($datetime::DEFAULT_TIME_FORMAT),
        ]);

        // Using Datetime format
        $datetime = new DateTimeUtils();
        $datetimeValue = $datetime->toString();
        $data = [
            'datetime' => $datetimeValue,
            'date' => $datetimeValue,
            'time' => $datetimeValue,
        ];

        $response = request_post('items/' . $collectionName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'datetime' => $datetime->toUTC()->toISO8601Format(),
            'date' => $datetime->toString($datetime::DEFAULT_DATE_FORMAT),
            'time' => $datetime->toString($datetime::DEFAULT_TIME_FORMAT),
        ]);

        // Using its format
        $datetime = new DateTimeUtils();
        $data = [
            'datetime' => $datetime->toISO8601Format(),
            'date' => $datetime->toString($datetime::DEFAULT_DATE_FORMAT),
            'time' => $datetime->toString($datetime::DEFAULT_TIME_FORMAT),
        ];

        $response = request_post('items/' . $collectionName, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'datetime' => $datetime->toUTC()->toISO8601Format(),
            'date' => $datetime->toString($datetime::DEFAULT_DATE_FORMAT),
            'time' => $datetime->toString($datetime::DEFAULT_TIME_FORMAT),
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
