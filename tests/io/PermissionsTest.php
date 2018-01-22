<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;

class PermissionsTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    protected $internQueryParams = [
        'access_token' => 'intern_token'
    ];

    public static function resetDatabase()
    {
        reset_table_id('directus_privileges', 14);
        reset_table_id('products', 5);
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
        // Intern can't see products
        $response = request_error_get('items/products', $this->internQueryParams);
        assert_response_error($this, $response);

        $data = [
            'group_id' => 3,
            'table_name' => 'products',
            'allow_view' => 2
        ];

        $response = request_post('permissions', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);

        // Intern can see products
        $response = request_get('items/products', $this->internQueryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testUpdate()
    {
        $productData = [
            'status' => 1,
            'name' => 'Good Product',
            'price' => 10.00
        ];
        request_post('items/products', $productData, ['query' => $this->queryParams]);

        // Intern can't update products
        $response = request_error_patch('items/products/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response);

        $data = [
            'allow_edit' => 2
        ];

        $response = request_patch('permissions/14', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge(['id' => 14], $data));

        // Intern can update products
        $data = [
            'name' => 'Excellent Product'
        ];
        $response = request_patch('items/products/5', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testGetOne()
    {
        $data = [
            'id' => 14,
            'table_name' => 'products',
            'allow_view' => 2,
            'allow_edit' => 2
        ];

        $response = request_get('permissions/14', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $response = request_get('permissions', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 14
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('permissions/14', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('permissions/14', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        // Intern can't see products
        $response = request_error_get('items/products', $this->internQueryParams);
        assert_response_error($this, $response);
    }
}
