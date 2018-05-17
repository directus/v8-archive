<?php

namespace Directus\Tests\Api\Io;

use Directus\Util\ArrayUtils;

class ItemsBatchTest extends \PHPUnit_Framework_TestCase
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

        truncate_table(static::$db, 'products');
        truncate_table(static::$db, 'categories');
        fill_table(static::$db, 'categories', [
            ['name' => 'Old Category']
        ]);
        fill_table(static::$db, 'products', static::$data);
        $uploadPath = realpath(__DIR__ . '/../../public/storage/uploads');
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
                $this->assertSame($newData[$key], $value);
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
            unset($data['price']);

            foreach ($data[$index] as $key => $value) {
                $this->assertArrayHasKey($key, $newData);
                $this->assertSame($newData[$key], $value);
            }
        }
    }
}
