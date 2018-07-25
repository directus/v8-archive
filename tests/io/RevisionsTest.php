<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;

class RevisionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected static $db;

    public static function setUpBeforeClass()
    {
        static::$db = create_db_connection();
        static::truncateTable();
        static::dropSampleTables();
        static::createSampleTables();
    }

    public static function tearDownAfterClass()
    {
        static::truncateTable();
        static::dropSampleTables();
    }

    public function testOne()
    {
        $this->assertSame(1, 1);
    }

    public function testTwo()
    {
        $this->assertSame(1, 1);
    }

    public function testNewItem()
    {
        $this->assertSame(0, table_count(static::$db, 'directus_revisions'));

        $response = request_post('items/test', ['name' => 'test 1'], ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);
        $this->assertSame(1, table_count(static::$db, 'directus_revisions'));

        request_post('items/test', ['name' => 'test 2'], ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);
        $this->assertSame(2, table_count(static::$db, 'directus_revisions'));

        request_post('items/test', ['name' => 'test 3'], ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);
        $this->assertSame(3, table_count(static::$db, 'directus_revisions'));
    }

    public function testUpdateItem()
    {
        $count = table_count(static::$db, 'directus_revisions');

        $response = request_patch('items/test/1', ['name' => 'test one'], ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);
        $this->assertSame($count+1, table_count(static::$db, 'directus_revisions'));
    }

    public function testDeleteItem()
    {
        // Delete does not create revision
        $count = table_count(static::$db, 'directus_revisions');

        $response = request_delete('items/test/2', ['query' => ['access_token' => 'token']]);
        assert_response_empty($this, $response);
        $this->assertSame($count, table_count(static::$db, 'directus_revisions'));
    }

    public function testItemRevisionsCount()
    {
        $response = request_get('items/test/1/revisions', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 2
        ]);
    }

    public function testItemRevisionsOffset()
    {
        $response = request_get('items/test/1/revisions/0', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'object'
        ]);

        $data = response_get_data($response);
        $this->assertInternalType('object', $data->delta);
        $this->assertSame('test 1', $data->delta->name);

        // second revision
        $response = request_get('items/test/1/revisions/1', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'object'
        ]);

        $data = response_get_data($response);
        $this->assertInternalType('object', $data->delta);
        $this->assertSame('test one', $data->delta->name);
    }

    public function testItemRevisionRevert()
    {
        $response = request_get('items/test/1/revisions/0', ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'object'
        ]);

        $originalData = response_get_data($response);
        $this->assertInternalType('object', $originalData->delta);

        $response = request_patch('items/test/1/revert/1', [], ['query' => ['access_token' => 'token']]);
        assert_response($this, $response);

        $currentData = response_get_data($response);
        $this->assertSame($originalData->delta->name, $currentData->name);
    }

    protected static function truncateTable()
    {
        truncate_table(static::$db, 'directus_activity');
        truncate_table(static::$db, 'directus_revisions');
    }

    protected static function createSampleTables()
    {
        if (!static::$db) {
            return;
        }

        create_table(static::$db, 'test');
        table_insert(static::$db, 'directus_collections', [
            'collection' => 'test'
        ]);
    }

    protected static function dropSampleTables()
    {
        drop_table(static::$db, 'test');
    }
}
