<?php

namespace Directus\Tests\Api\Io;

use Directus\Validator\Exception\InvalidRequestException;

class CollectionPresetsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $data = [
        ['view_type' => 'tabular', 'collection' => 'products', 'fields' => 'id,name'],
        ['view_type' => 'tabular', 'collection' => 'products', 'user' => 2],
        ['view_type' => 'tabular', 'collection' => 'orders', 'user' => 1],
        ['view_type' => 'tabular', 'collection' => 'categories', 'user' => 1],
        ['view_type' => 'tabular', 'collection' => 'orders', 'user' => 2],
        ['view_type' => 'tabular', 'collection' => 'customers', 'user' => 1]
    ];

    public static function resetDatabase()
    {
        $db = create_db_connection();
        truncate_table($db, 'directus_collection_presets');
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
        $path = 'collection_presets';

        $data = $this->data[0];
        $response = request_post($path, $data, ['query' => ['access_token' => 'token']]);

        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testCreateMissingViewType()
    {
        $data = ['collection' => 'products', 'fields' => 'id,name'];
        $response = request_error_post('collection_presets', $data, ['query' => ['access_token' => 'token']]);
        assert_response_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);
    }

    public function testUpdate()
    {
        $path = 'collection_presets/1';

        $data = [
            'collection' => 'products',
            'fields' => 'name,price'
        ];
        $response = request_patch($path, $data, ['query' => ['access_token' => 'token']]);

        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testGetOne()
    {
        $path = 'collection_presets/1';

        $data = [
            'id' => 1,
            'collection' => 'products',
            'fields' => 'name,price'
        ];

        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $path = 'collection_presets';

        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testDelete()
    {
        $path = 'collection_presets/1';
        $response = request_delete($path, ['query' => ['access_token' => 'token']]);

        assert_response_empty($this, $response);
    }

    public function testAllUserCollectionPresets()
    {
        $path = 'collection_presets';
        $data = $this->data;

        foreach ($data as $item) {
            request_post($path, $item, ['query' => ['access_token' => 'token']]);
        }

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'user' => 1
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'user' => 2
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);
    }

    public function testAllCollectionPresets()
    {
        $path = 'collection_presets';

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'collection' => 'products'
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $response = request_get($path, [
            'access_token' => 'token',
            'filter' => [
                'collection' => 'customers'
            ]
        ]);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }
}
