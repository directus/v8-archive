<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;

class GroupsTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    public static function setUpBeforeClass()
    {
        reset_table_id('directus_groups', 4);
    }

    public static function tearDownAfterClass()
    {
        reset_table_id('directus_groups', 4);
    }

    public function testCreate()
    {
        $data = [
            'name' => 'Manager'
        ];

        $response = request_post('groups', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'Manager Team',
            'description' => 'All Manager users'
        ];

        $response = request_patch('groups/4', $data, ['query' => $this->queryParams]);
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

        $response = request_get('groups/4', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $response = request_get('groups', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('groups/4', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('groups/4', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }
}
