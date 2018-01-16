<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Validator\Exception\InvalidRequestException;
use GuzzleHttp\Exception\BadResponseException;

class BookmarksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected static $db;

    protected $data = [
        [
            'title' => 'Directus Docs',
            'url' => 'https://docs.getdirectus.com',
            'user' => 1
        ],
        [
            'title' => 'Directus',
            'url' => 'https://getdirectus.com',
            'user' => 2
        ],
        [
            'title' => 'Directus',
            'url' => 'https://getdirectus.com',
            'user' => 2
        ],
        [
            'title' => 'Directus API',
            'url' => 'https://api.getdirectus.com',
            'user' => 2
        ]
    ];

    public static function setUpBeforeClass()
    {
        static::$db = create_db_connection();
        static::$db->execute('TRUNCATE `directus_bookmarks`;');
    }

    public static function tearDownAfterClass()
    {
        static::$db = null;
    }

    public function testCreate()
    {
        // =============================================================================
        // CREATE BOOKMARK WITH USER AS DEFAULT (THE AUTHENTICATED USER
        // =============================================================================

        $data = $this->data[0];
        unset($data['user']);

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $this->data[0]);

        // =============================================================================
        // CREATE WITH USER SET, ADMIN USER
        // =============================================================================

        $data = $this->data[1];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);

        // =============================================================================
        // TEST WITH METADATA
        // =============================================================================

        $data = $this->data[2];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token', 'meta' => '*']]);
        response_assert($this, $response);
        response_assert_meta($this, $response);
        response_assert_data_contains($this, $response, $data);

        // =============================================================================
        // CREATE WITH NO USER SET
        // =============================================================================

        $data = $this->data[3];
        unset($data['user']);

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'intern_token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $this->data[3]);

        // =============================================================================
        // CREATE WITH EMPTY BODY
        // =============================================================================

        $data = [];

        try {
            $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'intern_token']]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        response_assert_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);
    }

    public function testList()
    {
        $response = request_get('bookmarks', ['access_token' => 'token']);
        response_assert($this, $response, [
            'count' => 4,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        $i = 0;
        foreach ($result->data as $item) {
            $data = $this->data[$i];
            $this->assertSame($data['title'], $item->title);
            $this->assertSame($data['url'], $item->url);
            $this->assertSame($data['user'], $item->user);
            $this->assertSame($i+1, $item->id);
            $i++;
        }

        $this->assertSame($i, 4);

        $response = request_get('bookmarks', ['access_token' => 'intern_token']);
        response_assert($this, $response, [
            'count' => 4,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        $i = 0;
        foreach ($result->data as $item) {
            $data = $this->data[$i];
            $this->assertSame($data['title'], $item->title);
            $this->assertSame($data['url'], $item->url);
            $this->assertSame($data['user'], $item->user);
            $this->assertSame($i+1, $item->id);
            $i++;
        }

        $this->assertSame($i, 4);
    }

    public function testListMine()
    {
        $response = request_get('bookmarks/user/me', ['access_token' => 'token']);
        response_assert($this, $response, [
            'count' => 1,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        foreach ($result->data as $item) {
            $this->assertSame(1, $item->user);
        }

        $response = request_get('bookmarks/user/me', ['access_token' => 'intern_token']);
        response_assert($this, $response, [
            'count' => 3,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        foreach ($result->data as $item) {
            $this->assertSame(2, $item->user);
        }
    }

    public function testListUser()
    {
        $response = request_get('bookmarks/user/1', ['access_token' => 'token']);
        response_assert($this, $response, [
            'count' => 1,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        foreach ($result->data as $item) {
            $this->assertSame(1, $item->user);
        }

        $response = request_get('bookmarks/user/2', ['access_token' => 'token']);
        response_assert($this, $response, [
            'count' => 3,
            'data' => 'array'
        ]);

        $result = response_to_json($response);
        foreach ($result->data as $item) {
            $this->assertSame(2, $item->user);
        }
    }

    public function testOne()
    {
        $response = request_get('bookmarks/1', ['access_token' => 'token']);

        response_assert($this, $response);
        response_assert_data_contains($this, $response, $this->data[0]);
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'Directus Hosted',
            'url' => 'https://directus.io'
        ];

        $response = request_patch('bookmarks/2', $data, ['query' => ['access_token' => 'token']]);

        response_assert($this, $response);
        response_assert_data_contains($this, $response, array_merge([
            'id' => 2
        ], $data));
    }

    public function testUpdateEmpty()
    {
        $data = [];

        try {
            $response = request_patch('bookmarks/2', $data, ['query' => ['access_token' => 'token']]);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        response_assert_error($this, $response, [
            'status' => 400,
            'code' => InvalidRequestException::ERROR_CODE
        ]);
    }

    public function testDelete()
    {
        $response = request_delete('bookmarks/1', ['query' => ['access_token' => 'token']]);
        response_assert_empty($this, $response);

        try {
            $response = request_get('bookmarks/1', ['access_token' => 'token']);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        response_assert_error($this, $response, [
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);
    }
}
