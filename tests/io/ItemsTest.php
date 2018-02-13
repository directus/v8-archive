<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;
use Directus\Util\ArrayUtils;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    protected $systemTables = [
        'directus_activity',
        'directus_fields',
        'directus_files',
        'directus_groups',
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

        truncate_table(static::$db, 'products');
        truncate_table(static::$db, 'categories');
        fill_table(static::$db, 'categories', [
            ['name' => 'Old Category']
        ]);
        fill_table(static::$db, 'products', static::$data);
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
        $first = (array)array_shift($data);
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
        $response = request_get($path, ['access_token' => 'token', 'id' => 2]);
        assert_response($this, $response);

        // =============================================================================
        // FETCH BY CSV
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'id' => '2,3']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        // =============================================================================
        // FETCH BY CSV: One non-existent
        // =============================================================================
        $response = request_get($path, ['access_token' => 'token', 'id' => '2,3,10']);
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
        $newData = (array)$result->data;
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
        $newData = (array)$result->data;
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
                'name' => 'New Product'
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

        $this->assertInternalType('object', $products);
        $this->assertObjectHasAttribute('data', $products);
        $this->assertInternalType('array', $products->data);
        $this->assertSame($products->data[0]->name, $productsRelated[0]['name']);

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

        $response = request_get('items/products/8', ['access_token' => 'token', 'fields' => '*, images.*']);
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

        $this->assertInternalType('object', $images);
        $this->assertObjectHasAttribute('data', $images);
        $this->assertInternalType('array', $images->data);
        $this->assertSame($images->data[0]->title, $imagesRelated[0]['file_id']['title']);
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
}
