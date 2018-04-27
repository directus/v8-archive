<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;

class RolesTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    public static function resetDatabase()
    {
        reset_table_id(create_db_connection(), 'directus_roles', 4);
    }

    public static function setUpBeforeClass()
    {
        static::resetDatabase();
    }

    public static function tearDownAfterClass()
    {
        static::resetDatabase();
    }

    public function testCreate()
    {
        $data = [
            'name' => 'Manager'
        ];

        $response = request_post('roles', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'Manager Team',
            'description' => 'All Manager users'
        ];

        $response = request_patch('roles/4', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge(['id' => 4], $data));
    }

    public function testGetOne()
    {
        $data = [
            'id' => 4,
            'name' => 'Manager Team',
            'description' => 'All Manager users'
        ];

        $response = request_get('roles/4', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $response = request_get('roles', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('roles/4', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('roles/4', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }
}
