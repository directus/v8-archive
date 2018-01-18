<?php

namespace Directus\Tests\Api\Io;

class PreferencesTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        truncate_table(create_db_connection(), 'directus_preferences');
    }

    public function testCreate()
    {
        $path = 'preferences';

        $data = [
            'table_name' => 'products',
            'visible_fields' => 'id,name'
        ];
        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);

        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);
    }

    public function testUpdate()
    {
        $path = 'preferences/1';

        $data = [
            'table_name' => 'products',
            'visible_fields' => 'name,price'
        ];
        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);

        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);
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
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $path = 'preferences';

        $response = request_get($path, ['access_token' => 'token']);
        response_assert($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testDelete()
    {
        $path = 'preferences/1';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);

        response_assert_empty($this, $response);
    }
}
