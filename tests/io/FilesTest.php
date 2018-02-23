<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;

class FilesTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @var string
     */
    protected static $thumbsPath;

    public static function resetDatabase()
    {
        static::$uploadPath = realpath(__DIR__ . '/../../public/storage/uploads');
        static::$thumbsPath = static::$uploadPath . '/thumbs';

        static::$db = create_db_connection();
        reset_table_id(static::$db, 'directus_files', 2);
        reset_table_id(static::$db, 'directus_folders', 1);
        $uploadPath = static::$uploadPath;

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
        $this->assertTrue(file_exists(static::$thumbsPath . '/2.jpg'));
    }

    public function testUpdate()
    {
        $data = [
            'title' => 'Green background'
        ];

        $response = request_patch('files/2', $data, ['query' => $this->queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, array_merge(['id' => 2], $data));
    }

    public function testGetOne()
    {
        $data = [
            'id' => 2,
            'title' => 'Green background'
        ];

        $response = request_get('files/2', $this->queryParams);
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
        $this->assertTrue(file_exists(static::$thumbsPath . '/3.jpg'));
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

    public function testDelete()
    {
        $response = request_delete('files/2', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('files/2', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(file_exists(static::$uploadPath . '/' . static::$fileName));
        $this->assertFalse(file_exists(static::$thumbsPath . '/2.jpg'));

        // delete second file
        $response = request_delete('files/3', ['query' => $this->queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('files/3', $this->queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);

        $this->assertFalse(file_exists(static::$uploadPath . '/' . static::$fileName2));
        $this->assertFalse(file_exists(static::$thumbsPath . '/3.jpg'));
    }

    protected function getImageBase64()
    {
        // TODO: Allow the data alone
        // TODO: Guess the data type
        // TODO: Confirm is base64
        return 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAUDBAQEAwUEBAQFBQUGBwwIBwcHBw8LCwkMEQ8SEhEPERETFhwXExQaFRERGCEYGh0dHx8fExciJCIeJBweHx7/2wBDAQUFBQcGBw4ICA4eFBEUHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh4eHh7/wAARCAB4AKADASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAX/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFgEBAQEAAAAAAAAAAAAAAAAAAAUH/8QAFBEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AugILDAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/9k=';
    }
}
