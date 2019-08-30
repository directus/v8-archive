<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;
use Directus\Exception\BatchUploadNotAllowedException;

class FilesTest extends \PHPUnit\Framework\TestCase
{
    protected $queryParams = [
        'access_token' => 'token'
    ];

    protected static $db;

    /**
     * @var string
     */
    protected static $fileName = 'green.jpg';
    protected static $fileName2 = 'myphoto.jpg';

    /**
     * @var string
     */
    protected static $uploadPath;

    public static function resetDatabase()
    {
        static::$uploadPath = realpath(__DIR__ . '/../../public/uploads/_/originals');

        static::$db = create_db_connection();
        truncate_table(static::$db, 'directus_files');
        truncate_table(static::$db, 'directus_folders');

        clear_storage(static::$uploadPath);
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
        $name = static::$fileName;
        $data = [
            'filename' => $name,
            'data' => $this->getImageBase64()
        ];

        $response = request_post('files', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ['filename' => $name]);

        $this->assertTrue(file_exists(static::$uploadPath . '/' . $name));
    }

    public function testUnableBatchCreate()
    {
        $data = [
            [
                'filename' => 'test1.jpg',
                'data' => $this->getImageBase64()
            ], [
                'filename' => 'test2.jpg',
                'data' => $this->getImageBase64()
            ]
        ];

        $response = request_error_post('files', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'status' => 400,
            'code' => BatchUploadNotAllowedException::ERROR_CODE,
        ]);
    }

    public function testCreateWithMultipart()
    {
        $name = 'image.jpg';
        $title = 'new file';

        $response = request('POST', 'files', [
            'multipart' => [
                [
                    'name' => 'filename',
                    'contents' => $name
                ],
                [
                    'name' => 'title',
                    'contents' => $title,
                ],
                [
                    'name' => 'data',
                    'contents' => $this->getImageStream()
                ]
            ],
            'query' => $this->queryParams
        ]);

        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'filename' => $name,
            'title' => $title
        ]);

        $this->assertTrue(file_exists(static::$uploadPath . '/' . $name));
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'Green background'
        ];

        $response = request_patch('files/1', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge(['id' => 1], $data));
    }

    public function testUnableBatchUpdateWithFiles()
    {
        // Update multiple files with different data
        $data = [
            [
                'id' => 1,
                'filename' => 'test1.jpg',
                'data' => $this->getImageBase64()
            ], [
                'id' => 2,
                'filename' => 'test2.jpg',
                'data' => $this->getImageBase64()
            ]
        ];

        $response = request_error_patch('files', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'status' => 400,
            'code' => BatchUploadNotAllowedException::ERROR_CODE,
        ]);

        // Update multiple files with same data
        $data = [
            'filename' => 'test2.jpg',
            'data' => $this->getImageBase64()
        ];

        $response = request_error_patch('files/1,2', $data, ['query' => $this->queryParams]);
        assert_response_error($this, $response, [
            'status' => 400,
            'code' => BatchUploadNotAllowedException::ERROR_CODE,
        ]);
    }

    public function testGetOne()
    {
        $data = [
            'id' => 1,
            'title' => 'Green background'
        ];

        $response = request_get('files/1', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testCreateFileWithFolder()
    {
        $data = [
            'name' => 'photos'
        ];

        $response = request_post('files/folders', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
        $folder = response_get_data($response);

        // Upload file
        $name = static::$fileName2;
        $data = [
            'folder' => $folder->id,
            'filename' => $name,
            'data' => $this->getImageBase64()
        ];

        $response = request_post('files', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ['filename' => $name]);

        $this->assertTrue(file_exists(static::$uploadPath . '/' . $name));
    }

    public function testUpdateFolder()
    {
        $data = [
            'id' => 1,
            'name' => 'pictures',
            'parent_folder' => null
        ];

        $response = request_patch('files/folders/1', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testGetOneFolder()
    {
        $data = [
            'id' => 1,
            'name' => 'pictures',
            'parent_folder' => null
        ];

        $response = request_get('files/folders/1', $this->queryParams);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testListFolder()
    {
        $response = request_get('files/folders', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testDeleteFolder()
    {
        $response = request_delete('files/folders/1', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('files/folders/1', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }

    public function testList()
    {
        $response = request_get('files', $this->queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testBatchUpdateWithoutFiles()
    {
        // Update multiple files with different data
        $data = [
            [
                'id' => 1,
                'title' => 'one',
            ], [
                'id' => 2,
                'title' => 'two',
            ]
        ];

        $response = request_patch('files', $data, ['query' => $this->queryParams]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $items = response_get_data($response);
        $this->assertSame('one', $items[0]->title);
        $this->assertSame('two', $items[1]->title);

        // Update multiple files with same data
        $data = [
            'title' => 'one-two'
        ];

        $response = request_patch('files/1,2', $data, ['query' => $this->queryParams]);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);

        $items = response_get_data($response);
        $this->assertSame('one-two', $items[0]->title);
        $this->assertSame('one-two', $items[1]->title);
    }

    public function testDelete()
    {
        $response = request_delete('files/1', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('files/1', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(file_exists(static::$uploadPath . '/' . static::$fileName));

        // delete filename 2
        $response = request_delete('files/3', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('files/3', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(file_exists(static::$uploadPath . '/' . static::$fileName2));
    }

    public function testCreateWithoutExtension()
    {
        $name = 'image';
        $data = [
            'filename' => $name,
            'data' => $this->getImageBase64()
        ];

        $response = request_post('files', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, ['filename' => $name]);

        $this->assertTrue(file_exists(static::$uploadPath . '/' . $name));
    }

    protected function getImageBase64()
    {
        // TODO: Allow the data alone
        // TODO: Guess the data type
        // TODO: Confirm is base64
        return 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k=';
    }

    protected function getImageStream()
    {
        return fopen(__DIR__ . '/../assets/image.jpeg', 'r');
    }
}
