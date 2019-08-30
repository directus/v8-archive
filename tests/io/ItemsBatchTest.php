<?php

namespace Directus\Tests\Api\Io;

use Directus\Util\ArrayUtils;

class ItemsBatchTest extends \PHPUnit\Framework\TestCase
{
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

    public function testCreate()
    {
        $path = 'items/products';
        $data = [
            [
                'status' => 1,
                'name' => 'Special Product',
                'price' => 999.99,
                'category_id' => 1
            ],
            [
                'status' => 1,
                'name' => 'Special Product 2',
                'price' => 999.99,
                'category_id' => 1
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $result = response_to_object($response);
        foreach ((array)$result->data as $index => $newData) {
            $newData = (array)$newData;
            unset($data[$index]['price']);

            foreach ($data[$index] as $key => $value) {
                $this->assertArrayHasKey($key, $newData);
                $this->assertSame($newData[$key], $value, $key);
            }
        }
    }

    public function testUpdate()
    {
        $path = 'items/products';

        $data = [
            [
                'id' => 5,
                'name' => 'Xpecial Product',
            ],
            [
                'id' => 6,
                'name' => 'xpecial Product 2'
            ]
        ];

        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $result = response_to_object($response);
        foreach ($result->data as $index => $newData) {
            $newData = (array)$newData;

            foreach ($data[$index] as $key => $value) {
                $this->assertArrayHasKey($key, $newData);
                $this->assertSame($newData[$key], $value);
            }
        }
    }

    public function testUpdateSameData()
    {
        $path = 'items/products/5,6';

        $data = [
            'name' => 'Super Xpecial'
        ];

        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $result = response_to_object($response);
        foreach ($result->data as $index => $newData) {
            $newData = (array)$newData;

            foreach ($data as $key => $value) {
                $this->assertArrayHasKey($key, $newData);
                $this->assertSame($newData[$key], $value);
            }
        }
    }

    public function testCreateWithManyToOne()
    {
        $path = 'items/products';
        $data = [
            [
                'status' => 1,
                'name' => 'Premium Product 1',
                'price' => 9999.99,
                'category_id' => [
                    'name' => 'Premium Cat 1'
                ]
            ],
            [
                'status' => 1,
                'name' => 'Premium Product 2',
                'price' => 9999.99,
                'category_id' => [
                    'name' => 'Premium Cat 2'
                ]
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get('items/products/7,8', ['access_token' => 'token', 'fields' => '*, category_id.*']);
        $responseData = response_get_data($response);
        $this->assertInternalType('array', $responseData);

        foreach ($responseData as $index => $newData) {
            unset($data[$index]['price']);
            $newData = (array)$newData;

            $categoryId = ArrayUtils::pull($data[$index], 'category_id');
            $this->assertArrayHasKey('category_id', $newData);
            $category = ArrayUtils::pull($newData, 'category_id');

            foreach ($data[$index] as $key => $value) {
                $this->assertSame($value, $newData[$key]);
            }

            $this->assertInternalType('object', $category);
            $this->assertSame($category->name, $categoryId['name']);
        }
    }

    public function testCreateWithOneToMany()
    {
        $path = 'items/categories';
        $data = [
            [
                'name' => 'New Category 1',
                'products' => [[
                    'status' => '1',
                    'name' => 'New Product 1',
                    'price' => 100,
                ]]
            ],
            [
                'name' => 'New Category 2',
                'products' => [[
                    'status' => '1',
                    'name' => 'New Product 2',
                    'price' => 200,
                ]]
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get('items/categories/4,5', ['access_token' => 'token', 'fields' => '*, products.*']);
        $responseData = response_get_data($response);
        $this->assertInternalType('array', $responseData);

        foreach ($responseData as $index => $newData) {
            $newData = (array) $newData;
            $this->assertArrayHasKey('products', $newData);
            $products = ArrayUtils::pull($newData, 'products');
            $productsRelated = ArrayUtils::pull($data[$index], 'products');
            foreach ($data[$index] as $key => $value) {
                $this->assertSame($value, $newData[$key]);
            }

            $this->assertInternalType('array', $products);
            $this->assertSame($products[0]->name, $productsRelated[0]['name']);
        }
    }

    public function testCreateWithManyToMany()
    {
        $path = 'items/products';
        $data = [
            [
                'status' => 1,
                'name' => 'Limited Product 1',
                'price' => 1010.01,
                'images' => [
                    [
                        'file_id' => [
                            'filename' => 'potato1.jpg',
                            'title' => 'Image of a fake potato 1',
                            'data' => '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k='
                        ]
                    ]
                ]
            ],
            [
                'status' => 1,
                'name' => 'Limited Product 2',
                'price' => 1010.01,
                'images' => [
                    [
                        'file_id' => [
                            'filename' => 'potato2.jpg',
                            'title' => 'Image of a fake potato 2',
                            'data' => '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k='
                        ]
                    ]
                ]
            ]
        ];

        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get('items/products/11,12', ['access_token' => 'token', 'fields' => '*, images.*.*']);
        $responseData = response_get_data($response);
        $this->assertInternalType('array', $responseData);

        foreach ($responseData as $index => $newData) {
            $newData = (array)$newData;
            $this->assertArrayHasKey('images', $newData);
            $images = ArrayUtils::pull($newData, 'images');
            $imagesRelated = ArrayUtils::pull($data[$index], 'images');
            unset($data[$index]['price']);
            foreach ($data[$index] as $key => $value) {
                $this->assertSame($value, $newData[$key]);
            }

            $this->assertInternalType('array', $images);
            $this->assertSame($images[0]->file_id->title, $imagesRelated[0]['file_id']['title']);
        }
    }

    public function estDelete()
    {
        $path = 'items/products/5,6,7,8,9';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 0
        ]);

        $path = 'items/categories/3,4';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 0
        ]);
    }
}
