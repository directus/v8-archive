<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;
use Directus\Permissions\Exception\ForbiddenCollectionCreateException;
use Directus\Permissions\Exception\ForbiddenCollectionDeleteException;
use Directus\Permissions\Exception\ForbiddenCollectionReadException;
use Directus\Permissions\Exception\ForbiddenCollectionUpdateException;

class PermissionsTest extends \PHPUnit_Framework_TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    protected $internQueryParams = [
        'access_token' => 'intern_token'
    ];

    protected static $db = null;
    protected $internGroup = 3;
    protected $adminGroup = 1;

    protected static $data = [
        ['status' => 2, 'name' => 'Old Product', 'price' => 4.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Basic Product', 'price' => 9.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Premium Product', 'price' => 19.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Enterprise Product', 'price' => 49.99]
    ];

    public static function resetDatabase()
    {
        static::$db = $db = create_db_connection();
        reset_table_id($db, 'directus_permissions', 1);
        truncate_table($db, 'products');
        truncate_table($db, 'posts');
        fill_table($db, 'products', static::$data);
    }

    public static function setUpBeforeClass()
    {
        static::dropTestTable();
        static::createTestTable();
        static::resetDatabase();
    }

    public static function tearDownAfterClass()
    {
        static::resetDatabase();
        static::dropTestTable();
    }

    public function testCreate()
    {
        // Intern can't see products
        $response = request_error_get('items/products', $this->internQueryParams);
        assert_response_error($this, $response);

        $data = [
            'group' => 3,
            'collection' => 'products',
            'read' => 3
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
            'update' => 3
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
            'read' => 3,
            'update' => 3
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

    public function testCreateAndRead()
    {
        truncate_table(static::$db, 'directus_permissions');
        // Intern can't read posts
        $response = request_error_get('items/posts', $this->internQueryParams);
        assert_response_error($this, $response);

        // Intern can't create posts
        $data = [
            'title' => 'Post 1'
        ];

        $response = request_error_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionCreateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN create but CANNOT READ
        $this->addPermissionTo($this->internGroup, 'posts', [
            'create' => 1
        ]);

        $data = [
            'title' => 'Post 1',
            'status' => 1
        ];

        $response = request_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // Intern cannot read posts
        $response = request_error_get('items/products/1', $this->internQueryParams);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionReadException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern Can read their items
        $this->updatePermission(1, [
            'read' => 1
        ]);

        // Intern can read posts
        $response = request_get('items/posts/1', $this->internQueryParams);
        assert_response($this, $response);

        $data = ['title' => 'Post 2'];
        $response = request_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testStatusCreate()
    {
        truncate_table(static::$db, 'directus_permissions');

        // Intern CAN create draft but CANNOT READ any posts
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'create' => 1
        ]);

        // Intern cannot create draft posts
        $data = [
            'title' => 'Post 1',
            'status' => 1
        ];

        $response = request_error_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionCreateException::ERROR_CODE
        ]);

        // Intern cannot create posts with default status (which is null)
        $data = [
            'title' => 'Post 1'
        ];

        $response = request_error_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionCreateException::ERROR_CODE
        ]);

        // Intern CAN create draft and CAN READ their own posts
        $this->updatePermission(1, [
            'create' => 1,
            'read' => 1
        ]);

        $data = [
            'title' => 'Post 1',
            'status' => 2
        ];

        $response = request_post('items/posts', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testStatusRead()
    {
        truncate_table(static::$db, 'directus_permissions');
        truncate_table(static::$db, 'posts');

        // Intern CAN read draft and published (1) but CANNOT READ deleted (0)
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'read' => 1
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 1,
            'read' => 1
        ]);

        $data0 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 2];
        table_insert(static::$db, 'posts', $data0);

        $data1 = ['title' => 'Published Post', 'status' => 1, 'author' => 2];
        table_insert(static::$db, 'posts', $data1);

        $data2 = ['title' => 'Draft Post', 'status' => 2, 'author' => 2];
        table_insert(static::$db, 'posts', $data2);

        $data3 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 2];
        table_insert(static::$db, 'posts', $data3);

        $response = request_error_get('items/posts/1', $this->internQueryParams);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionReadException::ERROR_CODE
        ]);

        $response = request_get('items/posts/2', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data1);

        $response = request_get('items/posts/3', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data2);

        $response = request_error_get('items/posts/4', $this->internQueryParams);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionReadException::ERROR_CODE
        ]);

        $response = request_get('items/posts', $this->internQueryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $response = request_get('items/posts', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $response = request_get('items/posts', array_merge($this->internQueryParams, ['status' => '0,1,2']));
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get('items/posts', array_merge($this->queryParams, ['status' => '0,1,2']));
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testStatusUpdate()
    {
        truncate_table(static::$db, 'directus_permissions');
        truncate_table(static::$db, 'posts');

        $this->resetTestPosts();

        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts
        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_error_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts BUT CANNOT read any posts
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => null,
            'update' => 1
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'update' => 2
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'update' => 3
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // UPDATE + READ PERMISSION
        // ----------------------------------------------------------------------------

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own posts
        $this->updatePermission(1, [
            'update' => 3,
            'read' => 1
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        $response = request_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);



        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own group posts
        $this->updatePermission(1, [
            'update' => 3,
            'read' => 2
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        $response = request_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts AND CAN read any posts
        $this->updatePermission(1, [
            'update' => 3,
            'read' => 3
        ]);

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        $response = request_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);

        // ----------------------------------------------------------------------------
        // PERMISSION BY STATUS
        // ----------------------------------------------------------------------------
        truncate_table(static::$db, 'directus_permissions');

        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts with any status
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 0,
            'update' => 0
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 1,
            'update' => 0
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'update' => 0
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_error_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/3', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/4', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/8', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 0
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'update' => 1
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'update' => 2
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'update' => 3
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 1
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 1
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/3', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 2
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 3
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 1
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 2
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'update' => 3
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/6', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'update' => 1
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/posts/11', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'update' => 2
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/posts/11', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'update' => 3
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/posts/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/7', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/posts/11', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
    }

    public function testStatusDelete()
    {
        truncate_table(static::$db, 'directus_permissions');
        $this->resetTestPosts();

        $data = $this->getPostsData();

        // ----------------------------------------------------------------------------
        // Intern CANNOT delete posts

        $response = request_error_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN delete their own posts BUT CANNOT read any posts
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => null,
            'delete' => 1
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'delete' => 2
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'delete' => 3
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // UPDATE + READ PERMISSION
        // ----------------------------------------------------------------------------

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own posts
        $this->updatePermission(1, [
            'delete' => 3,
            'read' => 1
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own group posts
        $this->updatePermission(1, [
            'delete' => 3,
            'read' => 2
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts AND CAN read any posts
        $this->updatePermission(1, [
            'delete' => 3,
            'read' => 3
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // PERMISSION BY STATUS
        // ----------------------------------------------------------------------------
        truncate_table(static::$db, 'directus_permissions');

        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts with any status
        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 0,
            'delete' => 0
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 1,
            'delete' => 0
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'delete' => 0
        ]);

        $response = request_error_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/3', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/4', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/8', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 0
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'delete' => 1
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'delete' => 2
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(1, [
            'status' => 0,
            'delete' => 3
        ]);

        $response = request_delete('items/posts/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/posts/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // WITH STATUS: 1
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 1
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/posts/3', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 2
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 3
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 6], $data['data5']));

        $response = request_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 1
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 2
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => 1,
            'delete' => 3
        ]);

        $response = request_delete('items/posts/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 2], $data['data1']));

        $response = request_delete('items/posts/6', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 6], $data['data5']));

        $response = request_delete('items/posts/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'delete' => 1
        ]);

        $response = request_delete('items/posts/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 3], $data['data2']));

        $response = request_error_delete('items/posts/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/posts/11', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'delete' => 2
        ]);

        $response = request_delete('items/posts/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 3], $data['data2']));

        $response = request_error_delete('items/posts/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/posts/11', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 11], $data['data10']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(3, [
            'status' => 2,
            'delete' => 3
        ]);

        $response = request_delete('items/posts/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 3], $data['data2']));

        $response = request_delete('items/posts/7', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 7], $data['data6']));

        $response = request_delete('items/posts/11', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, 'posts', array_merge(['id' => 11], $data['data10']));
    }

    public function testFieldBlacklist()
    {
        $this->resetTestPosts();
        truncate_table(static::$db, 'directus_permissions');
        $this->addPermissionTo($this->internGroup, 'posts', [
            'create' => 1,
            'read' => 1,
            // 'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status,author'
        ]);

        $response = request_post('items/posts', ['title' => 'one'], ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);

        $response = request_get('items/posts/1', array_merge(
            $this->internQueryParams,
            ['fields' => 'id,title,author']
        ));

        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);

        $this->updatePermission(1, [
            'read_field_blacklist' => 'author'
        ]);

        $response = request_get('items/posts/1', $this->internQueryParams);
        assert_response($this, $response, [
            'has_fields' => true,
            'fields' => ['id', 'title', 'status']
        ]);

        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title', 'status']);

        $response = request_get('items/posts/1', array_merge(
            $this->internQueryParams,
            ['fields' => 'id,title,author,status']
        ));
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title', 'status']);
    }

    public function testStatusFieldBlacklist()
    {
        $this->resetTestPosts();
        truncate_table(static::$db, 'directus_permissions');

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 0,
            'read' => 1,
            'write_field_blacklist' => 'author,status',
            // 'read_field_blacklist' => 'status,author'
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 1,
            'read' => 1,
            'write_field_blacklist' => 'author,status',
            // 'read_field_blacklist' => 'status,author'
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'read' => 1,
            'write_field_blacklist' => 'author,status',
            // 'read_field_blacklist' => 'status'
        ]);

        // ----------------------------------------------------------------------------
        // STATUS: 0
        // ----------------------------------------------------------------------------
        // Read
        // $response = request_get('items/posts/1', $this->internQueryParams);
        // assert_response($this, $response);
        // assert_response_data_fields($this, $response, ['id', 'title']);
        // ----------------------------------------------------------------------------
        // Write
        // ----------------------------------------------------------------------------
        $data = ['status' => 0, 'title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['status' => 0, 'title' => 'Post 1'];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        // ----------------------------------------------------------------------------
        // STATUS: 1
        // ----------------------------------------------------------------------------
        // Read
        // $response = request_get('items/posts/2', $this->internQueryParams);
        // assert_response($this, $response);
        // assert_response_data_fields($this, $response, ['id', 'title', 'author', 'status']);
        // ----------------------------------------------------------------------------
        // Write
        // ----------------------------------------------------------------------------
        $data = ['status' => 1, 'title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['status' => 1, 'title' => 'Post 1'];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        // ----------------------------------------------------------------------------
        // STATUS: 2
        // ----------------------------------------------------------------------------
        // Read
        // $response = request_get('items/posts/3', $this->internQueryParams);
        // assert_response($this, $response);
        // assert_response_data_fields($this, $response, ['id', 'title', 'author']);
        // ----------------------------------------------------------------------------
        // Write
        // ----------------------------------------------------------------------------
        $data = ['status' => 2, 'title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['status' => 2, 'title' => 'Post 1'];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $this->resetTestPosts();
        truncate_table(static::$db, 'directus_permissions');

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => null,
            'read' => 1,
            'write_field_blacklist' => 'author,status',
            // 'read_field_blacklist' => 'status,author'
        ]);

        $data = ['status' => 2, 'title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['status' => 2, 'title' => 'Post 1'];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);
    }

    protected function addPermissionTo($group, $collection, array $data)
    {
        $data = array_merge($data, [
            'group' => $group,
            'collection' => $collection
        ]);

        $response = request_post('permissions', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    protected function updatePermission($id, $data)
    {
        $response = request_patch('permissions/' . $id, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    protected static function createTestTable()
    {
        $data = [
            'collection' => 'posts',
            'fields' => [
                ['field' => 'id', 'interface' => 'primary_key', 'type' => 'integer', 'auto_increment' => true],
                ['field' => 'status', 'interface' => 'status', 'type' => 'integer'],
                ['field' => 'title', 'interface' => 'text_input', 'type' => 'varchar', 'length' => 100],
                ['field' => 'author', 'interface' => 'user_created', 'type' => 'integer'],
            ]
        ];

        $response = request_post('collections', $data, ['query' => ['access_token' => 'token']]);
    }

    protected static function dropTestTable()
    {
        $response = request_error_delete('collections/posts', ['query' => ['access_token' => 'token']]);
    }

    protected function getPostsData()
    {
        // ----------------------------------------------------------------------------
        // Intern user
        // ----------------------------------------------------------------------------
        $data0 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 2];
        $data1 = ['title' => 'Published Post', 'status' => 1, 'author' => 2];
        $data2 = ['title' => 'Draft Post', 'status' => 2, 'author' => 2];
        $data3 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 2];

        // ----------------------------------------------------------------------------
        // Admin group
        // ----------------------------------------------------------------------------
        $data4 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 1];
        $data5 = ['title' => 'Published Post', 'status' => 1, 'author' => 1];
        $data6 = ['title' => 'Draft Post', 'status' => 2, 'author' => 1];
        $data7 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 1];

        // ----------------------------------------------------------------------------
        // Group user
        // ----------------------------------------------------------------------------
        $data8 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 3];
        $data9 = ['title' => 'Published Post', 'status' => 1, 'author' => 3];
        $data10 = ['title' => 'Draft Post', 'status' => 2, 'author' => 3];
        $data11 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 3];

        $variables = [];
        for ($i = 0; $i <= 11; $i++) {
            $variables[] = 'data' . $i;
        }

        return compact(
            $variables
        );
    }

    protected function resetTestPosts()
    {
        truncate_table(static::$db, 'posts');

        $data = $this->getPostsData();
        extract($data);

        // ----------------------------------------------------------------------------
        // Intern user
        // ----------------------------------------------------------------------------
        // $data0 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 2];
        table_insert(static::$db, 'posts', $data0);

        // $data1 = ['title' => 'Published Post', 'status' => 1, 'author' => 2];
        table_insert(static::$db, 'posts', $data1);

        // $data2 = ['title' => 'Draft Post', 'status' => 2, 'author' => 2];
        table_insert(static::$db, 'posts', $data2);

        // $data3 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 2];
        table_insert(static::$db, 'posts', $data3);

        // ----------------------------------------------------------------------------
        // Admin group
        // ----------------------------------------------------------------------------
        // $data4 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 1];
        table_insert(static::$db, 'posts', $data4);

        // $data5 = ['title' => 'Published Post', 'status' => 1, 'author' => 1];
        table_insert(static::$db, 'posts', $data5);

        // $data6 = ['title' => 'Draft Post', 'status' => 2, 'author' => 1];
        table_insert(static::$db, 'posts', $data6);

        // $data7 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 1];
        table_insert(static::$db, 'posts', $data7);

        // ----------------------------------------------------------------------------
        // Group user
        // ----------------------------------------------------------------------------
        // $data8 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 3];
        table_insert(static::$db, 'posts', $data8);

        // $data9 = ['title' => 'Published Post', 'status' => 1, 'author' => 3];
        table_insert(static::$db, 'posts', $data9);

        // $data10 = ['title' => 'Draft Post', 'status' => 2, 'author' => 3];
        table_insert(static::$db, 'posts', $data10);

        // $data11 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 3];
        table_insert(static::$db, 'posts', $data11);
    }
}
