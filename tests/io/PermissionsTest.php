<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;
use Directus\Permissions\Acl;
use Directus\Permissions\Exception\ForbiddenCollectionCreateException;
use Directus\Permissions\Exception\ForbiddenCollectionDeleteException;
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
        truncate_table($db, 'articles');
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
            'role' => 3,
            'collection' => 'products',
            'read' => Acl::LEVEL_FULL
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
            'update' => Acl::LEVEL_FULL
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
            'read' => Acl::LEVEL_FULL,
            'update' => Acl::LEVEL_FULL
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
                'role' => 3
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
            'create' => Acl::LEVEL_MINE
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
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern Can read their items
        $this->updatePermission(1, [
            'read' => Acl::LEVEL_MINE
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
        $this->tryStatusCreate('posts', 2, 1);
    }

    public function testStringStatusCreate()
    {
        $this->tryStatusCreate('articles', 'draft', 'published');
    }

    public function testStatusRead()
    {
        $publishedStatus = 1;
        $draftStatus = 2;
        $noPermissionStatusOne = 0;
        $noPermissionStatusTwo = 3;
        $this->tryStatusRead('posts', $publishedStatus, $draftStatus, $noPermissionStatusOne, $noPermissionStatusTwo);
    }

    public function testStringStatusRead()
    {
        $publishedStatus = 'published';
        $draftStatus = 'draft';
        $noPermissionStatusOne = 'deleted';
        $noPermissionStatusTwo = 'under_review';
        $this->tryStatusRead('articles', $publishedStatus, $draftStatus, $noPermissionStatusOne, $noPermissionStatusTwo);
    }

    public function testStatusUpdate()
    {
        $this->tryStatusUpdate('posts');
    }

    public function testStringUpdate()
    {
        $this->tryStatusUpdate('articles');
    }

    public function testStatusDelete()
    {
        $this->tryStatusDelete('posts');
    }

    public function testStringStatusDelete()
    {
        $this->tryStatusDelete('articles');
    }

    public function tryStatusDelete($collection)
    {
        truncate_table(static::$db, 'directus_permissions');
        $this->resetTestItems($collection);
        $data = $this->getItemsData($this->getStatuses($collection));

        // ----------------------------------------------------------------------------
        // Intern CANNOT delete posts

        $response = request_error_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN delete their own posts BUT CANNOT read any posts
        $this->addPermissionTo($this->internGroup, $collection, [
            'status' => null,
            'delete' => Acl::LEVEL_MINE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'delete' => Acl::LEVEL_ROLE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'delete' => Acl::LEVEL_FULL
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // UPDATE + READ PERMISSION
        // ----------------------------------------------------------------------------

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own posts
        $this->updatePermission(1, [
            'delete' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_MINE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own group posts
        $this->updatePermission(1, [
            'delete' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_ROLE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts AND CAN read any posts
        $this->updatePermission(1, [
            'delete' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_FULL
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // PERMISSION BY STATUS
        // ----------------------------------------------------------------------------
        truncate_table(static::$db, 'directus_permissions');

        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts with any status
        $statuses = $this->getStatuses($collection);
        foreach ($statuses as $status) {
            $this->addPermissionTo($this->internGroup, $collection, [
                'status' => $status,
                'delete' => Acl::LEVEL_NONE
            ]);
        }

        $response = request_error_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/3', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/4', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/8', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 0
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $currentStatus = $statuses[0];
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_MINE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_ROLE
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_FULL
        ]);

        $response = request_delete('items/'.$collection.'/1', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 1], $data['data0']));

        $response = request_error_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/'.$collection.'/5', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 5], $data['data4']));

        $response = request_delete('items/'.$collection.'/9', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 9], $data['data8']));

        // ----------------------------------------------------------------------------
        // WITH STATUS: 1
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $currentStatus = $statuses[1];
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_MINE
        ]);

        $response = request_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/'.$collection.'/3', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/10', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_ROLE
        ]);

        $response = request_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 2], $data['data1']));

        $response = request_error_delete('items/'.$collection.'/6', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/'.$collection.'/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_FULL
        ]);

        $response = request_delete('items/'.$collection.'/2', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 2], $data['data1']));

        $response = request_delete('items/'.$collection.'/6', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 6], $data['data5']));

        $response = request_delete('items/'.$collection.'/10', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 10], $data['data9']));

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $currentStatus = $statuses[2];
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_MINE
        ]);

        $response = request_delete('items/'.$collection.'/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 3], $data['data2']));

        $response = request_error_delete('items/'.$collection.'/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_error_delete('items/'.$collection.'/11', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_ROLE
        ]);

        $response = request_delete('items/'.$collection.'/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 3], $data['data2']));

        $response = request_error_delete('items/'.$collection.'/7', ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionDeleteException::ERROR_CODE
        ]);

        $response = request_delete('items/'.$collection.'/11', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 11], $data['data10']));

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'delete' => Acl::LEVEL_FULL
        ]);

        $response = request_delete('items/'.$collection.'/3', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 3], $data['data2']));

        $response = request_delete('items/'.$collection.'/7', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 7], $data['data6']));

        $response = request_delete('items/'.$collection.'/11', ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
        table_insert(static::$db, $collection, array_merge(['id' => 11], $data['data10']));
    }

    public function testFieldBlacklist()
    {
        $this->resetTestPosts();
        truncate_table(static::$db, 'directus_permissions');
        $this->addPermissionTo($this->internGroup, 'posts', [
            'create' => Acl::LEVEL_MINE,
            'read' => Acl::LEVEL_MINE,
            // 'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status,author'
        ]);

        $response = request_post('items/posts', ['title' => 'one'], ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);

        $response = request_get('items/posts/2', array_merge(
            $this->internQueryParams,
            ['fields' => 'id,title,author']
        ));

        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);

        $this->updatePermission(1, [
            'read_field_blacklist' => 'author'
        ]);

        $response = request_get('items/posts/2', $this->internQueryParams);
        assert_response($this, $response, [
            'has_fields' => true,
            'fields' => ['id', 'title', 'status']
        ]);

        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title', 'status']);

        $response = request_get('items/posts/2', array_merge(
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
            'read' => Acl::LEVEL_MINE,
            'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status,author'
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 1,
            'read' => Acl::LEVEL_MINE,
            'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status,author'
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 2,
            'read' => Acl::LEVEL_MINE,
            'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status'
        ]);

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => 3,
            'read' => Acl::LEVEL_MINE,
            'read_field_blacklist' => 'id,status,title,author'
        ]);

        // ----------------------------------------------------------------------------
        // STATUS: 0
        // ----------------------------------------------------------------------------
        // Read
        // ----------------------------------------------------------------------------
        $response = request_error_get('items/posts/1', $this->internQueryParams);
        assert_response_error($this, $response);
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
        // ----------------------------------------------------------------------------
        $response = request_get('items/posts/2', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);
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
        // ----------------------------------------------------------------------------
        $response = request_get('items/posts/3', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title', 'author']);
        // ----------------------------------------------------------------------------
        // Write
        // ----------------------------------------------------------------------------
        $data = ['status' => 2, 'title' => 'Post 1', 'author' => 1];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);

        $data = ['status' => 2, 'title' => 'Post 1'];
        $response = request_error_post('items/posts', $data, $this->internQueryParams);
        assert_response_error($this, $response);


        // ----------------------------------------------------------------------------
        // STATUS: 3
        // ----------------------------------------------------------------------------
        // Read
        // ----------------------------------------------------------------------------
        $response = request_error_get('items/posts/4', $this->internQueryParams);
        assert_response_error($this, $response);

        // FETCH ALL
        // Status 3 should be removed as the user cannot read any fields
        $response = request_get('items/posts', array_merge(['status' => '*'], $this->internQueryParams));
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $items = response_get_data($response);
        $item0 = $items[0];
        assert_data_fields($this, $item0, ['id', 'title']);

        $item1 = $items[1];
        assert_data_fields($this, $item1, ['id', 'title', 'author']);

        $this->resetTestPosts();
        truncate_table(static::$db, 'directus_permissions');

        $this->addPermissionTo($this->internGroup, 'posts', [
            'status' => null,
            'read' => Acl::LEVEL_MINE,
            'write_field_blacklist' => 'author,status',
            'read_field_blacklist' => 'status,author'
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

        // One item
        $response = request_get('items/posts/2', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_fields($this, $response, ['id', 'title']);

        // List of items
        $response = request_get('items/posts', $this->internQueryParams);
        assert_response($this, $response, [
            'data' => 'array'
        ]);
        assert_response_data_fields($this, $response, ['id', 'title']);
    }

    protected function addPermissionTo($group, $collection, array $data)
    {
        $data = array_merge($data, [
            'role' => $group,
            'collection' => $collection
        ]);

        $response = request_post('permissions', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data, false);
    }

    protected function updatePermission($id, $data)
    {
        $response = request_patch('permissions/' . $id, $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data, false);
    }

    protected function tryStatusCreate($collection, $statusWithPermission, $statusWithoutPermission)
    {
        truncate_table(static::$db, 'directus_permissions');

        // Intern CAN create draft but CANNOT READ any items
        $this->addPermissionTo($this->internGroup, $collection, [
            'status' => $statusWithPermission,
            'create' => Acl::LEVEL_MINE
        ]);

        // Intern cannot create draft posts
        $data = [
            'title' => 'Post 1',
            'status' => $statusWithoutPermission
        ];

        $response = request_error_post('items/' . $collection, $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionCreateException::ERROR_CODE
        ]);

        // Intern can create posts with default status (because it is 2)
        $data = [
            'title' => 'Post 1'
        ];

        $response = request_post('items/' . $collection, $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // Intern CAN create draft and CAN READ their own posts
        $this->updatePermission(1, [
            'create' => Acl::LEVEL_MINE,
            'read' => Acl::LEVEL_MINE
        ]);

        $data = [
            'title' => 'Post 1',
            'status' => $statusWithPermission
        ];

        $response = request_post('items/' . $collection, $data, ['query' => $this->internQueryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    protected function tryStatusRead($collection, $publishedStatus, $draftStatus, $noPermissionStatusOne, $noPermissionStatusTwo)
    {
        truncate_table(static::$db, 'directus_permissions');
        truncate_table(static::$db, $collection);

        // Intern CAN read draft and published (1) but CANNOT READ deleted (0)
        $this->addPermissionTo($this->internGroup, $collection, [
            'status' => $publishedStatus,
            'read' => Acl::LEVEL_MINE
        ]);

        $this->addPermissionTo($this->internGroup, $collection, [
            'status' => $draftStatus,
            'read' => Acl::LEVEL_MINE
        ]);

        $data0 = ['title' => 'Deleted Post', 'status' => $noPermissionStatusOne, 'author' => 2];
        table_insert(static::$db, $collection, $data0);

        $data1 = ['title' => 'Published Post', 'status' => $publishedStatus, 'author' => 2];
        table_insert(static::$db, $collection, $data1);

        $data2 = ['title' => 'Draft Post', 'status' => $draftStatus, 'author' => 2];
        table_insert(static::$db, $collection, $data2);

        $data3 = ['title' => 'Under Review Post', 'status' => $noPermissionStatusTwo, 'author' => 2];
        table_insert(static::$db, $collection, $data3);

        $response = request_error_get('items/'.$collection.'/1', $this->internQueryParams);
        assert_response_error($this, $response, [
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);

        $response = request_get('items/'.$collection.'/2', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data1);

        $response = request_get('items/'.$collection.'/3', $this->internQueryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data2);

        $response = request_error_get('items/'.$collection.'/4', $this->internQueryParams);
        assert_response_error($this, $response, [
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);

        $response = request_get('items/' . $collection, $this->internQueryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $response = request_get('items/' . $collection, $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $statuses = implode(',', [
            $noPermissionStatusOne,
            $publishedStatus,
            $draftStatus
        ]);
        $response = request_get('items/' . $collection, array_merge($this->internQueryParams, ['status' => $statuses]));
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get('items/' . $collection, array_merge($this->queryParams, ['status' => $statuses]));
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    protected function tryStatusUpdate($collection)
    {
        truncate_table(static::$db, 'directus_permissions');
        truncate_table(static::$db, $collection);

        $this->resetTestItems($collection);

        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts
        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_error_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts BUT CANNOT read any posts
        $this->addPermissionTo($this->internGroup, $collection, [
            'status' => null,
            'update' => Acl::LEVEL_MINE
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'update' => Acl::LEVEL_ROLE
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CANNOT read any posts
        $this->updatePermission(1, [
            'update' => Acl::LEVEL_FULL
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // UPDATE + READ PERMISSION
        // ----------------------------------------------------------------------------

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own posts
        $this->updatePermission(1, [
            'update' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_MINE
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts BUT CAN only read their own group posts
        $this->updatePermission(1, [
            'update' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_ROLE
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts AND CAN read any posts
        $this->updatePermission(1, [
            'update' => Acl::LEVEL_FULL,
            'read' => Acl::LEVEL_FULL
        ]);

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // PERMISSION BY STATUS
        // ----------------------------------------------------------------------------
        truncate_table(static::$db, 'directus_permissions');

        $statuses = $this->getStatuses($collection);
        // ----------------------------------------------------------------------------
        // Intern CANNOT update posts with any status
        foreach ($statuses as $status) {
            $this->addPermissionTo($this->internGroup, $collection, [
                'status' => $status,
                'update' => Acl::LEVEL_NONE
            ]);
        }

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_error_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/3', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/4', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/8', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 0
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 0
        $currentStatus = $statuses[0];
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_MINE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 0
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_ROLE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 0
        $this->updatePermission(1, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_FULL
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/1', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/'.$collection.'/5', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/9', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 1
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 1
        $currentStatus = $statuses[1];
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_MINE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/3', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/10', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 1
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_ROLE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/6', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/'.$collection.'/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 1
        $this->updatePermission(2, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_FULL
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/2', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/6', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/10', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // WITH STATUS: 2
        // ----------------------------------------------------------------------------
        // Intern CAN update their own posts with status = 2
        $currentStatus = $statuses[2];
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_MINE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/'.$collection.'/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_error_patch('items/'.$collection.'/11', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        // ----------------------------------------------------------------------------
        // Intern CAN update their own group posts with status = 2
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_ROLE
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_error_patch('items/posts/7', $data, ['query' => $this->internQueryParams]);
        assert_response_error($this, $response, [
            'status' => 403,
            'code' => ForbiddenCollectionUpdateException::ERROR_CODE
        ]);

        $response = request_patch('items/'.$collection.'/11', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        // ----------------------------------------------------------------------------
        // Intern CAN update any posts with status = 2
        $this->updatePermission(3, [
            'status' => $currentStatus,
            'update' => Acl::LEVEL_FULL
        ]);

        $data = [
            'title' => 'Post title changed'
        ];

        $response = request_patch('items/'.$collection.'/3', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/7', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);

        $response = request_patch('items/'.$collection.'/11', $data, ['query' => $this->internQueryParams]);
        assert_response_empty($this, $response);
    }

    protected static function createTestTable()
    {
        $statusOptions = [
            'status_mapping' => [
                2 => ['name' => 'Draft', 'published' => false],
                1 => ['name' => 'Published']
            ]
        ];
        $data = [
            'collection' => 'posts',
            'fields' => [
                ['field' => 'id', 'interface' => 'primary_key', 'type' => 'primary_key', 'datatype' => 'integer', 'primary_key' => true, 'auto_increment' => true, 'length' => 10],
                ['field' => 'status', 'interface' => 'status', 'type' => 'status', 'datatype' => 'integer', 'length' => 10, 'default_value' => 2, 'options' => $statusOptions],
                ['field' => 'title', 'interface' => 'text_input', 'type' => 'varchar', 'length' => 100],
                ['field' => 'author', 'type' => 'user_created', 'interface' => 'user_created', 'datatype' => 'integer', 'length' => 10],
            ]
        ];

        $response = request_post('collections', $data, ['query' => ['access_token' => 'token']]);

        $statusOptions = [
            'status_mapping' => [
                'draft' => ['name' => 'Draft', 'published' => false],
                'published' => ['name' => 'Published']
            ]
        ];
        $data = [
            'collection' => 'articles',
            'fields' => [
                ['field' => 'id', 'interface' => 'primary_key', 'type' => 'primary_key', 'datatype' => 'integer', 'primary_key' => true, 'auto_increment' => true, 'length' => 10],
                ['field' => 'status', 'interface' => 'status', 'type' => 'status', 'datatype' => 'varchar', 'length' => 16, 'default_value' => 'draft', 'options' => $statusOptions],
                ['field' => 'title', 'interface' => 'text_input', 'type' => 'varchar', 'length' => 100],
                ['field' => 'author', 'type' => 'user_created', 'interface' => 'user_created', 'datatype' => 'integer', 'length' => 10],
            ]
        ];

        $response = request_post('collections', $data, ['query' => ['access_token' => 'token']]);
    }

    protected static function dropTestTable()
    {
        $response = request_error_delete('collections/posts', ['query' => ['access_token' => 'token']]);
        $response = request_error_delete('collections/articles', ['query' => ['access_token' => 'token']]);
    }

    protected function getStatuses($collection)
    {
        switch ($collection) {
            case 'articles':
                $statuses = ['deleted', 'published', 'draft', 'under_review'];
                break;
            default:
                $statuses = [0, 1, 2, 3];
        }

        return $statuses;
    }

    protected function getPostsData()
    {
        return $this->getItemsData($this->getStatuses('posts'));
    }

    protected function getArticlesData()
    {
        return $this->getItemsData($this->getStatuses('articles'));
    }

    protected function getItemsData(array $statuses)
    {
        $statusOne = array_shift($statuses);
        $statusTwo = array_shift($statuses);
        $statusThree = array_shift($statuses);
        $statusFour = array_shift($statuses);
        // ----------------------------------------------------------------------------
        // Intern user
        // ----------------------------------------------------------------------------
        $data0 = ['title' => 'Deleted Post', 'status' => $statusOne, 'author' => 2];
        $data1 = ['title' => 'Published Post', 'status' => $statusTwo, 'author' => 2];
        $data2 = ['title' => 'Draft Post', 'status' => $statusThree, 'author' => 2];
        $data3 = ['title' => 'Under Review Post', 'status' => $statusFour, 'author' => 2];

        // ----------------------------------------------------------------------------
        // Admin group
        // ----------------------------------------------------------------------------
        $data4 = ['title' => 'Deleted Post', 'status' => $statusOne, 'author' => 1];
        $data5 = ['title' => 'Published Post', 'status' => $statusTwo, 'author' => 1];
        $data6 = ['title' => 'Draft Post', 'status' => $statusThree, 'author' => 1];
        $data7 = ['title' => 'Under Review Post', 'status' => $statusFour, 'author' => 1];

        // ----------------------------------------------------------------------------
        // Group user
        // ----------------------------------------------------------------------------
        $data8 = ['title' => 'Deleted Post', 'status' => $statusOne, 'author' => 3];
        $data9 = ['title' => 'Published Post', 'status' => $statusTwo, 'author' => 3];
        $data10 = ['title' => 'Draft Post', 'status' => $statusThree, 'author' => 3];
        $data11 = ['title' => 'Under Review Post', 'status' => $statusFour, 'author' => 3];

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
        $this->resetTestItems('posts');
    }

    protected function resetTestArticles()
    {
        $this->resetTestItems('articles');
    }

    protected function resetTestItems($collection)
    {
        truncate_table(static::$db, $collection);

        if ($collection == 'articles' ) {
            $data = $this->getArticlesData();
        } else {
            $data = $this->getPostsData();
        }
        extract($data);

        // ----------------------------------------------------------------------------
        // Intern user
        // ----------------------------------------------------------------------------
        // $data0 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 2];
        table_insert(static::$db, $collection, $data0);

        // $data1 = ['title' => 'Published Post', 'status' => 1, 'author' => 2];
        table_insert(static::$db, $collection, $data1);

        // $data2 = ['title' => 'Draft Post', 'status' => 2, 'author' => 2];
        table_insert(static::$db, $collection, $data2);

        // $data3 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 2];
        table_insert(static::$db, $collection, $data3);

        // ----------------------------------------------------------------------------
        // Admin group
        // ----------------------------------------------------------------------------
        // $data4 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 1];
        table_insert(static::$db, $collection, $data4);

        // $data5 = ['title' => 'Published Post', 'status' => 1, 'author' => 1];
        table_insert(static::$db, $collection, $data5);

        // $data6 = ['title' => 'Draft Post', 'status' => 2, 'author' => 1];
        table_insert(static::$db, $collection, $data6);

        // $data7 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 1];
        table_insert(static::$db, $collection, $data7);

        // ----------------------------------------------------------------------------
        // Group user
        // ----------------------------------------------------------------------------
        // $data8 = ['title' => 'Deleted Post', 'status' => 0, 'author' => 3];
        table_insert(static::$db, $collection, $data8);

        // $data9 = ['title' => 'Published Post', 'status' => 1, 'author' => 3];
        table_insert(static::$db, $collection, $data9);

        // $data10 = ['title' => 'Draft Post', 'status' => 2, 'author' => 3];
        table_insert(static::$db, $collection, $data10);

        // $data11 = ['title' => 'Under Review Post', 'status' => 3, 'author' => 3];
        table_insert(static::$db, $collection, $data11);
    }
}
