<?php

namespace Directus\Tests\Api\Io;

class PreferencesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $data = [
        ['table_name' => 'products', 'visible_fields' => 'id,name'],
        ['table_name' => 'products', 'user' => 2],
        ['table_name' => 'orders', 'user' => 1],
        ['table_name' => 'categories', 'user' => 1],
        ['table_name' => 'orders', 'user' => 2],
        ['table_name' => 'customers', 'user' => 1]
    ];

    public static function setUpBeforeClass()
    {
        truncate_table(create_db_connection(), 'directus_preferences');
    }

    public function testCreate()
    {
        $path = 'preferences';

        $data = $this->data[0];
        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);

        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testUpdate()
    {
        $path = 'preferences/1';

        $data = [
            'table_name' => 'products',
            'visible_fields' => 'name,price'
        ];
        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);

        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testGetOne()
    {
        $path = 'preferences/1';

        $data = [
            'id' => 1,
            'table_name' => 'products',
            'visible_fields' => 'name,price'
        ];

        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $path = 'preferences';

        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testDelete()
    {
        $path = 'preferences/1';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);

        assert_response_empty($this, $response);
    }

    public function testAllUserPreferences()
    {
        $path = 'preferences';
        $data = $this->data;

        foreach ($data as $item) {
            request_post($path, $item, ['query' => ['access_token' => 'token']]);
        }

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'user' => 1
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'user' => 2
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);
    }

    public function testAllTablePreferences()
    {
        $path = 'preferences';

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'table_name' => 'products'
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'table_name' => 'customers'
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }
}
