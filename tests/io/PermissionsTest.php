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

    protected static $data = [
        ['status' => 2, 'name' => 'Old Product', 'price' => 4.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Basic Product', 'price' => 9.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Premium Product', 'price' => 19.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Enterprise Product', 'price' => 49.99]
    ];

    public static function resetDatabase()
    {
        $db = create_db_connection();
        reset_table_id($db, 'directus_permissions', 1);
        truncate_table($db, 'products');
        fill_table($db, 'products', static::$data);
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
            'group' => 3,
            'collection' => 'products',
            'read' => 2
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
            'update' => 2
        ];

        $response = request_patch('permissions/1', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge(['id' => 1], $data));

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
            'id' => 1,
            'collection' => 'products',
            'read' => 2,
            'update' => 2
        ];

        $response = request_get('permissions/1', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $response = request_get('permissions', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testGroupPermissions()
    {
        $params = array_merge([
            'filter' => [
                'group' => 3
            ]
        ], $this->queryParams);

        $response = request_get('permissions', $params);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testTablePermissions()
    {
        $params = array_merge([
            'filter' => [
                'collection' => 'products'
            ]
        ], $this->queryParams);

        $response = request_get('permissions', $params);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('permissions/1', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('permissions/1', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        // Intern can't see products
        $response = request_error_get('items/products', $this->internQueryParams);
        assert_response_error($this, $response);
    }
}
