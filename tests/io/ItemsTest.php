<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Permissions\Acl;
use Directus\Util\ArrayUtils;

class ItemsTest extends \PHPUnit\Framework\TestCase
{
    protected $systemTables = [
        'directus_activity',
        'directus_fields',
        'directus_files',
        'directus_roles',
        'directus_collection_presets',
        'directus_permissions',
        'directus_settings',
        'directus_collections',
        'directus_users'
    ];

    protected static $data = [
        ['status' => 2, 'name' => 'Old Product', 'price' => 4.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Basic Product', 'price' => 9.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Premium Product', 'price' => 19.99, 'category_id' => 1],
        ['status' => 1, 'name' => 'Enterprise Product', 'price' => 49.99]
    ];

    protected static $db;

    public static function resetData()
    {
        static::$db = create_db_connection();

        truncate_table(static::$db, 'products_images');
        truncate_table(static::$db, 'products');
        truncate_table(static::$db, 'categories');
        fill_table(static::$db, 'categories', [
            ['name' => 'Old Category']
        ]);
        fill_table(static::$db, 'products', static::$data);
        $uploadPath = realpath(__DIR__ . '/../../public/uploads/_/originals');
        clear_storage($uploadPath);

        request_patch('fields/products/status', ['options' => [
            'status_mapping' => [
                '1' => [
                    'name' => 'Published'
                ],
                2 => [
                    'name' => 'Draft',
                    'published' => false
                ]
            ]
        ]], ['query' => ['access_token' => 'token']]);
    }

    public static function setUpBeforeClass()
    {
        static::resetData();
    }

    public static function tearDownAfterClass()
    {
        static::resetData();
    }

    public function testNotDirectAccess()
    {
        // Fetching items
        foreach ($this->systemTables as $table) {
            $path = 'items/' . $table;
            $response = request_error_get($path);

            assert_response_error($this, $response, [
                'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                'status' => 401
            ]);
        }

        // Creating Item
        foreach ($this->systemTables as $table) {
            $path = 'items/' . $table;
            request_error_post($path, [
                'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                'status' => 401
            ]);
        }

        // Fetching a Item
        foreach ($this->systemTables as $table) {
            foreach (['GET', 'PATCH', 'PUT', 'DELETE'] as $method) {
                $path = 'items/' . $table . '/1';
                call_user_func('request_error_' . strtolower($method), $path, [
                    'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                    'status' => 401
                ]);
            }
        }

        // Batch
        foreach ($this->systemTables as $table) {
            foreach (['POST', 'PATCH', 'PUT', 'DELETE'] as $method) {
                $path = 'items/' . $table . '/batch';
                call_user_func('request_error_' . strtolower($method), $path, [
                    'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                    'status' => 401
                ]);
            }
        }
    }

    public function testListFields()
    {
        $fields = [
            'id',
            'status',
            'name',
            'price',
            'category_id'
        ];
        $path = 'items/products';

        // =============================================================================
        // GET ALL FIELDS
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3,
            'fields' => $fields
        ]);

        // TODO: Fix casting DECIMAL
        // it's converting 9.99 to things like 9.9900000000000002131628207280300557613372802734375
        // assert_response_data_contains($this, $response, static::$data);

        // =============================================================================
        // GET ALL FIELDS WITH ASTERISK
        // =============================================================================
        $response = request_get($path, [
            'fields' => '*',
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3,
            'fields' => $fields
        ]);

        // =============================================================================
        // GET SINGLE FIELD
        // =============================================================================
        foreach ($fields as $field) {
            // =============================================================================
            // GET ALL FIELDS WITH ASTERISK
            // =============================================================================
            $response = request_get($path, [
                'fields' => $field,
                'access_token' => 'token'
            ]);

            assert_response($this, $response, [
                'data' => 'array',
                'count' => 3,
                'fields' => [
                    $field
                ]
            ]);
        }

        // =============================================================================
        // GET ALL FIELDS WITH CSV
        // =============================================================================
        $response = request_get($path, [
            'fields' => implode(',', $fields),
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3,
            'fields' => $fields
        ]);

        // =============================================================================
        // GET SOME FIELDS WITH CSV
        // =============================================================================
        $response = request_get($path, [
            'fields' => 'id, name',
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3,
            'fields' => ['id', 'name']
        ]);
    }

    public function testItemFields()
    {
        $fields = [
            'id',
            'status',
            'name',
            'price',
            'category_id'
        ];
        $path = 'items/products/2';

        // =============================================================================
        // GET ALL FIELDS
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'fields' => $fields
        ]);

        // =============================================================================
        // GET ALL FIELDS WITH ASTERISK
        // =============================================================================
        $response = request_get($path, [
            'fields' => '*',
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'fields' => $fields
        ]);

        // =============================================================================
        // GET SINGLE FIELD
        // =============================================================================
        foreach ($fields as $field) {
            // =============================================================================
            // GET ALL FIELDS WITH ASTERISK
            // =============================================================================
            $response = request_get($path, [
                'fields' => $field,
                'access_token' => 'token'
            ]);

            assert_response($this, $response, [
                'fields' => [
                    $field
                ]
            ]);
        }

        // =============================================================================
        // GET ALL FIELDS WITH CSV
        // =============================================================================
        $response = request_get($path, [
            'fields' => implode(',', $fields),
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'fields' => $fields
        ]);

        // =============================================================================
        // GET SOME FIELDS WITH CSV
        // =============================================================================
        $response = request_get($path, [
            'fields' => 'id, name',
            'access_token' => 'token'
        ]);
        assert_response($this, $response, [
            'fields' => ['id', 'name']
        ]);
    }

    public function testLimit()
    {
        $path = 'items/products';

        // =============================================================================
        // TEST LIMIT (lower than items count)
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'limit' => 1]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        // =============================================================================
        // TEST LIMIT (higher than items count)
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'limit' => 5]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testOffset()
    {
        $path = 'items/products';

        // =============================================================================
        // TEST LIMIT (lower than items count)
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'limit' => 1, 'offset' => 1]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $result = response_to_object($response);
        $data = $result->data;
        $first = (array) array_shift($data);
        $expected = static::$data[2];
        // because the output is casted wrong
        unset($expected['price']);
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $first);
            $this->assertSame($value, $first[$key]);
        }
    }

    public function testId()
    {
        $path = 'items/products';

        // =============================================================================
        // FETCH BY ONE ID: Return a single object, not an array of one item
        // =============================================================================
        $response = request_get($path . '/2', ['access_token' => 'token']);
        assert_response($this, $response);

        // =============================================================================
        // FETCH BY CSV
        // =============================================================================
        $response = request_get($path . '/2,3', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        // =============================================================================
        // FETCH BY CSV: One non-existent
        // =============================================================================
        $response = request_get($path . '/2,3,10', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);
    }

    public function testStatus()
    {
        $path = 'items/products';

        // =============================================================================
        // TEST WITH ASTERISK: ALL STATUSES
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'status' => '*']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);

        // =============================================================================
        // TEST WITH ONE STATUS
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'status' => 1]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);

        $response = request_get($path, ['access_token' => 'token', 'status' => 2]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        // =============================================================================
        // TEST WITH CSV STATUSES
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'status' => '1,2']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);

        // =============================================================================
        // TEST WITHOUT PARAM
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testSort()
    {
        $path = 'items/products';

        // =============================================================================
        // TEST DEFAULT SORTING
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token']);
        $result = response_to_object($response);
        $data = $result->data;

        $first = array_shift($data);
        $last = array_pop($data);
        $expectedFirst = static::$data[1];
        $expectedLast = static::$data[3];

        $this->assertSame($expectedFirst['name'], $first->name);
        $this->assertSame($expectedLast['name'], $last->name);

        // =============================================================================
        // TEST DEFAULT SORTING SAME AS ID
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'sort' => 'id']);
        $result = response_to_object($response);
        $data = $result->data;

        $first = array_shift($data);
        $last = array_pop($data);
        $expectedFirst = static::$data[1];
        $expectedLast = static::$data[3];

        $this->assertSame($expectedFirst['name'], $first->name);
        $this->assertSame($expectedLast['name'], $last->name);

        // =============================================================================
        // TEST DESCENDING SORTING
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'sort' => '-id']);
        $result = response_to_object($response);
        $data = $result->data;

        $first = array_shift($data);
        $last = array_pop($data);
        $expectedFirst = static::$data[3];
        $expectedLast = static::$data[1];

        $this->assertSame($expectedFirst['name'], $first->name);
        $this->assertSame($expectedLast['name'], $last->name);
    }

    public function testQ()
    {
        $path = 'items/products';

        $response = request_get($path, ['access_token' => 'token', 'q' => 'Product']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);

        $response = request_get($path, ['access_token' => 'token', 'q' => 'Basic']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        $response = request_get($path, ['access_token' => 'token', 'q' => 'Old Product']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 0
        ]);
    }

    public function testFilterEqual()
    {
        $path = 'items/products';

        // =============================================================================
        // EQUAL ID: HIDDEN STATUS
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['id' => 1]
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 0
        ]);

        // =============================================================================
        // EQUAL ID: VISIBLE STATUS
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['id' => 2]
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        // =============================================================================
        // EQUAL NAME
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['name' => 'Basic Product']
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        // =============================================================================
        // EQ - EQUAL ID: HIDDEN STATUS
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['id' => ['eq' => 1]]
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 0
        ]);

        // =============================================================================
        // EQ - EQUAL ID: VISIBLE STATUS
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['id' => ['eq' => 2]]
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);

        // =============================================================================
        // EQ - EQUAL NAME
        // =============================================================================
        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => ['name' => ['eq' => 'Basic Product']]
        ]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testCreate()
    {
        $path = 'items/products';
        $data = [
            'status' => 1,
            'name' => 'Special Product',
            'price' => 999.99,
            'category_id' => 1
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $result = response_to_object($response);
        $newData = (array) $result->data;
        unset($data['price']);

        foreach ($data as $key => $value) {
            $this->assertArrayHasKey($key, $newData);
            $this->assertSame($newData[$key], $value);
        }
    }

    public function testUpdate()
    {
        $path = 'items/products/5';
        $data = [
            'name' => 'Xpecial Product',
            'category_id' => 2
        ];

        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $result = response_to_object($response);
        $newData = (array) $result->data;
        unset($data['price']);

        foreach ($data as $key => $value) {
            $this->assertArrayHasKey($key, $newData);
            $this->assertSame($newData[$key], $value);
        }
    }

    public function testCreateWithRelation()
    {
        $path = 'items/products';
        $data = [
            'status' => 1,
            'name' => 'Premium Product',
            'price' => 9999.99,
            'category_id' => [
                'name' => 'Premium Cat'
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $response = request_get('items/products/6', ['access_token' => 'token', 'fields' => '*, category_id.*']);
        $responseDataObject = response_get_data($response);
        $this->assertInternalType('object', $responseDataObject);
        $responseDataArray = (array) $responseDataObject;

        unset($data['price']);
        $categoryId = ArrayUtils::pull($data, 'category_id');
        $this->assertArrayHasKey('category_id', $responseDataArray);
        $category = ArrayUtils::pull($responseDataArray, 'category_id');
        foreach ($data as $key => $value) {
            $this->assertSame($value, $responseDataArray[$key]);
        }

        $this->assertInternalType('object', $category);
        $this->assertSame($category->name, $categoryId['name']);

        // =============================================================================
        // TEST O2M
        // =============================================================================
        $path = 'items/categories';
        $data = [
            'name' => 'New Category',
            'products' => [[
                'status' => 1,
                'name' => 'New Product',
                'price' => 100
            ]]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $response = request_get('items/categories/3', ['access_token' => 'token', 'fields' => '*, products.*']);
        $responseDataObject = response_get_data($response);
        $this->assertInternalType('object', $responseDataObject);
        $responseDataArray = (array) $responseDataObject;

        $this->assertArrayHasKey('products', $responseDataArray);
        $products = ArrayUtils::pull($responseDataArray, 'products');
        $productsRelated = ArrayUtils::pull($data, 'products');
        foreach ($data as $key => $value) {
            $this->assertSame($value, $responseDataArray[$key]);
        }

        $this->assertInternalType('array', $products);
        $this->assertSame($products[0]->name, $productsRelated[0]['name']);

        // =============================================================================
        // TEST M2M
        // =============================================================================
        $path = 'items/products';
        $data = [
            'status' => 1,
            'name' => 'Limited Product',
            'price' => 1010.01,
            'images' => [
                [
                    'file_id' => [
                        'filename' => 'potato.jpg',
                        'title' => 'Image of a fake potato',
                        'data' => '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k='
                    ]
                ]
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $response = request_get('items/products/8', ['access_token' => 'token', 'fields' => '*, images.*.*']);
        $responseDataObject = response_get_data($response);
        $this->assertInternalType('object', $responseDataObject);
        $responseDataArray = (array) $responseDataObject;

        $this->assertArrayHasKey('images', $responseDataArray);
        $images = ArrayUtils::pull($responseDataArray, 'images');
        $imagesRelated = ArrayUtils::pull($data, 'images');
        unset($data['price']);
        foreach ($data as $key => $value) {
            $this->assertSame($value, $responseDataArray[$key]);
        }

        $this->assertInternalType('array', $images);
        $this->assertSame($images[0]->file_id->title, $imagesRelated[0]['file_id']['title']);
    }

    public function testList()
    {
        $path = 'items/categories';
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);

        $path = 'items/products';
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 7
        ]);
    }

    public function testStatusInterfaceMapping()
    {
        request_patch(
            'fields/products/status',
            ['options' => null],
            ['query' => ['access_token' => 'token'], 'json' => true]
        );
        truncate_table(static::$db, 'directus_settings');

        $response = request_error_get('items/products', ['access_token' => 'token']);
        assert_response_error($this, $response);

        // {"status_mapping": {"1":{"name":"Published","published":true},"2":{"name":"Draft","published":false}}}
        // Insert global status mapping
        $data = [
            'scope' => 'status',
            'key' => 'status_mapping',
            'value' => json_encode([
                'one' => [
                    'name' => 'Published'
                ],
                'two' => [
                    'name' => 'Draft'
                ]
            ])
        ];
        request_post('settings', $data, ['query' => ['access_token' => 'token']]);

        $response = request_error_get('items/products', ['access_token' => 'token']);
        assert_response_error($this, $response);

        // update global status mapping
        $data = [
            'value' => json_encode([
                '1' => [
                    'name' => 'Published'
                ],
                '2' => [
                    'name' => 'Draft',
                    'published' => false
                ]
            ])
        ];
        request_patch('settings/1', $data, ['query' => ['access_token' => 'token']]);

        $response = request_get('items/products', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array'
        ]);

        // update status mapping on field interface with string-type
        $options = [
            'status_mapping' => [
                'one' => [
                    'name' => 'Published'
                ],
                'two' => [
                    'name' => 'Draft'
                ]
            ]
        ];
        request_patch('fields/products/status', ['options' => $options], ['query' => ['access_token' => 'token']]);

        $response = request_error_get('items/products', ['access_token' => 'token']);
        assert_response_error($this, $response);

        // update status mapping on field interface with integer-type
        $options = [
            'status_mapping' => [
                1 => [
                    'name' => 'Published'
                ],
                2 => [
                    'name' => 'Draft'
                ]
            ]
        ];
        request_patch('fields/products/status', ['options' => $options], ['query' => ['access_token' => 'token']]);

        $response = request_get('items/products', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array'
        ]);

        // update status mapping on field interface with numeric-type
        $options = [
            'status_mapping' => [
                '1' => [
                    'name' => 'Published'
                ],
                '2' => [
                    'name' => 'Draft'
                ]
            ]
        ];
        request_patch('fields/products/status', ['options' => $options], ['query' => ['access_token' => 'token']]);

        $response = request_get('items/products', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array'
        ]);
    }

    public function testDelete()
    {
        $path = 'items/products/5';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);

        $path = 'items/products/6';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);

        $path = 'items/categories/3';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);
    }

    public function testSoftDeleteNonAdmins()
    {
        $data = array_merge([
            'role' => 3,
            'collection' => 'products',
            'status' => 1
        ], Acl::PERMISSION_FULL);

        $response = request_post('permissions', $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $path = 'items/products';
        $data = ['name' => 'deleted product', 'status' => 0, 'price' => 0];
        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        // Admin
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 6
        ]);

        $response = request_get($path, ['access_token' => 'token', 'status' => '*']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 7
        ]);

        $response = request_get($path . '/9', ['access_token' => 'token', 'status' => '*']);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ArrayUtils::omit($data, 'price'));

        // Non-Admin
        $response = request_get($path, ['access_token' => 'intern_token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 5
        ]);

        $response = request_get($path, ['access_token' => 'intern_token', 'status' => '*']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 5
        ]);

        $response = request_error_get($path . '/9', ['access_token' => 'intern_token', 'status' => '*']);
        assert_response_error($this, $response, [
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);
    }

    public function testLangOneItem()
    {
        $path = 'items/news/1';
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('translations', $result);
        $this->assertInternalType('array', $result->translations);
        $this->assertCount(3, $result->translations);

        // wildcard
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*', 'lang' => '*']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('translations', $result);
        $this->assertInternalType('array', $result->translations);
        $this->assertCount(3, $result->translations);

        // one language
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*', 'lang' => 'en']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('translations', $result);
        $this->assertInternalType('array', $result->translations);
        $this->assertCount(1, $result->translations);

        // non-existing language
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*', 'lang' => 'de']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('translations', $result);
        $this->assertInternalType('array', $result->translations);
        $this->assertCount(0, $result->translations);
    }

    public function testLangList()
    {
        $path = 'items/news';
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*']);
        $result = response_get_data($response);

        foreach ($result as $item) {
            $this->assertObjectHasAttribute('translations', $item);
            $this->assertInternalType('array', $item->translations);
        }
    }

    public function testLangRelatedOneItem()
    {
        $path = 'items/home/1';
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*.*.*']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('news', $result);
        $homeNews = $result->news;
        $this->assertInternalType('array', $homeNews);
        $this->assertObjectHasAttribute('news_id', $homeNews[0]);
        $news = $homeNews[0]->news_id;
        $this->assertObjectHasAttribute('translations', $news);
        $this->assertInternalType('array', $news->translations);
        $this->assertCount(3, $news->translations);

        // wildcard
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*.*.*', 'lang' => '*']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('news', $result);
        $homeNews = $result->news;
        $this->assertInternalType('array', $homeNews);
        $this->assertObjectHasAttribute('news_id', $homeNews[0]);
        $news = $homeNews[0]->news_id;
        $this->assertObjectHasAttribute('translations', $news);
        $this->assertInternalType('array', $news->translations);
        $this->assertCount(3, $news->translations);

        // one language
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*.*.*', 'lang' => 'en']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('news', $result);
        $homeNews = $result->news;
        $this->assertInternalType('array', $homeNews);
        $this->assertObjectHasAttribute('news_id', $homeNews[0]);
        $news = $homeNews[0]->news_id;
        $this->assertObjectHasAttribute('translations', $news);
        $this->assertInternalType('array', $news->translations);
        $this->assertCount(1, $news->translations);

        // non-existing language
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*.*.*.*', 'lang' => 'de']);
        $result = response_get_data($response);

        $this->assertObjectHasAttribute('news', $result);
        $homeNews = $result->news;
        $this->assertInternalType('array', $homeNews);
        $this->assertObjectHasAttribute('news_id', $homeNews[0]);
        $news = $homeNews[0]->news_id;
        $this->assertObjectHasAttribute('translations', $news);
        $this->assertInternalType('array', $news->translations);
        $this->assertCount(0, $news->translations);
    }

    public function testWithRelationAndParams()
    {
        $path = 'items/products';
        $data = [
            'status' => 1,
            'name' => 'Test',
            'price' => 9999.99,
            'category_id' => [
                'name' => 'Premium Cat'
            ]
        ];

        $testM2O = function ($response, $data) {
            assert_response($this, $response);
            $responseDataObject = response_get_data($response);
            $this->assertInternalType('object', $responseDataObject);
            $responseDataArray = (array) $responseDataObject;

            unset($data['price']);
            $categoryId = ArrayUtils::pull($data, 'category_id');
            $this->assertArrayHasKey('category_id', $responseDataArray);
            $category = ArrayUtils::pull($responseDataArray, 'category_id');
            foreach ($data as $key => $value) {
                $this->assertSame($value, $responseDataArray[$key]);
            }

            $this->assertInternalType('object', $category);
            $this->assertSame($category->name, $categoryId['name']);
        };

        $response = request_post($path, $data, ['query' => ['fields' => '*.*', 'access_token' => 'token']]);
        $testM2O($response, $data);
        $id = response_get_data($response)->id;

        $response = request_get('items/products/' . $id, ['access_token' => 'token', 'fields' => '*, category_id.*']);
        $testM2O($response, $data);

        $newData = ['name' => 'Test 2'];
        $response = request_patch('items/products/' . $id, $newData, ['query' => ['fields' => '*.*', 'access_token' => 'token']]);
        $testM2O($response, array_merge($data, $newData));

        // TEST O2M
        $path = 'items/categories';
        $data = [
            'name' => 'New Category',
            'products' => [[
                'status' => 1,
                'name' => 'New Product',
                'price' => 100
            ]]
        ];

        $testO2M = function ($response, $data) {
            assert_response($this, $response);
            $responseDataObject = response_get_data($response);
            $this->assertInternalType('object', $responseDataObject);
            $responseDataArray = (array) $responseDataObject;

            $this->assertArrayHasKey('products', $responseDataArray);
            $products = ArrayUtils::pull($responseDataArray, 'products');
            $productsRelated = ArrayUtils::pull($data, 'products');
            foreach ($data as $key => $value) {
                $this->assertSame($value, $responseDataArray[$key]);
            }

            $this->assertInternalType('array', $products);
            $this->assertSame($products[0]->name, $productsRelated[0]['name']);
        };

        $response = request_post($path, $data, ['query' => ['access_token' => 'token', 'fields' => '*, products.*']]);
        $testO2M($response, $data);
        $id = response_get_data($response)->id;

        $response = request_get('items/categories/' . $id, ['access_token' => 'token', 'fields' => '*, products.*']);
        $testO2M($response, $data);

        $newData = ['name' => 'New Category 2'];
        $response = request_patch('items/categories/' . $id, $newData, ['query' => ['fields' => '*, products.*', 'access_token' => 'token']]);
        $testO2M($response, array_merge($data, $newData));

        // TEST M2M
        $path = 'items/products';
        $data = [
            'status' => 1,
            'name' => 'Limited Product',
            'price' => 1010.01,
            'images' => [
                [
                    'file_id' => [
                        'filename' => 'potato.jpg',
                        'title' => 'Image of a fake potato',
                        'data' => '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k='
                    ]
                ]
            ]
        ];

        $testM2M = function ($response, $data) {
            assert_response($this, $response);

            $responseDataObject = response_get_data($response);
            $this->assertInternalType('object', $responseDataObject);
            $responseDataArray = (array) $responseDataObject;

            $this->assertArrayHasKey('images', $responseDataArray);
            $images = ArrayUtils::pull($responseDataArray, 'images');
            $imagesRelated = ArrayUtils::pull($data, 'images');
            unset($data['price']);
            foreach ($data as $key => $value) {
                $this->assertSame($value, $responseDataArray[$key]);
            }

            $this->assertInternalType('array', $images);
            $this->assertSame($images[0]->file_id->title, $imagesRelated[0]['file_id']['title']);
        };

        $response = request_post($path, $data, ['query' => ['fields' => '*, images.*.*', 'access_token' => 'token']]);
        $testM2M($response, $data);
        $id = response_get_data($response)->id;

        $response = request_get('items/products/' .  $id, ['access_token' => 'token', 'fields' => '*, images.*.*']);
        $testM2M($response, $data);

        $newData = ['name' => 'Limited Product 2'];
        $response = request_patch('items/products/' . $id, $newData, ['query' => ['fields' => '*, images.*.*', 'access_token' => 'token']]);
        $testM2M($response, array_merge($data, $newData));
    }

    public function testListMeta()
    {
        $path = 'items/products';
        $response = request_get($path, ['access_token' => 'token', 'meta' => '*']);
        assert_response($this, $response, [
            'data' => 'array'
        ]);
        assert_response_meta($this, $response, [
            'collection' => 'products',
            'type' => 'collection',
        ]);
        assert_response_meta_fields($this, $response, [
            'collection',
            'type',
            'result_count',
            'total_count',
            'status_count',
        ]);
    }

    public function testItemMeta()
    {
        $path = 'items/products/1';
        $response = request_get($path, ['access_token' => 'token', 'meta' => '*']);
        assert_response($this, $response);
        assert_response_meta($this, $response, [
            'collection' => 'products',
            'type' => 'item',
        ]);
        assert_response_meta_fields($this, $response, [
            'collection',
            'type',
        ]);
    }
}
