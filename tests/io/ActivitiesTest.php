<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;
use Directus\Database\Exception\ItemNotFoundException;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use GuzzleHttp\Exception\RequestException;

class ActivitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $db;

    public function setUp()
    {
        $this->db = create_db_connection();

        $this->truncateTable();
        $this->createSampleTables();

        $startDay = date('d') - 15;
        for ($i = 0; $i < 15; $i++) {
            $datetime = sprintf('2017-12-26 15:52:37', $startDay);
            $query = "INSERT INTO `directus_activity` (`id`, `type`, `action`, `identifier`, `table_name`, `row_id`, `user`, `data`, `delta`, `parent_id`, `parent_table`, `parent_changed`, `datetime`, `logged_ip`, `user_agent`)";
            $query.= "VALUES (DEFAULT, 'LOGIN', 'LOGIN', NULL, 'directus_users', 0, 1, NULL, NULL, NULL, NULL, 0, '" . $datetime . "', '::1', 'GuzzleHttp/6.2.1 curl/7.52.1 PHP/5.5.38');";

            $startDay++;
            $this->db->execute($query);
        }
    }

    public function tearDown()
    {
        $this->truncateTable();
        $this->dropSampleTables();
    }

    public function testColumns()
    {
        $columns = [
            'id',
            'type',
            'action',
            'identifier',
            'table_name',
            'row_id',
            'user',
            'data',
            'delta',
            'parent_id',
            'parent_table',
            'parent_changed',
            'datetime',
            'logged_ip',
            'user_agent'
        ];

        $path = 'activities';

        // Not selecting columns
        $response = request_get($path, ['access_token' => 'token']);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using Asterisk
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*']);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using a list of columns (array)
        $response = request_get($path, ['access_token' => 'token', 'fields' => $columns]);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using a list of columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $columns)]);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Selecting some columns (array)
        $someColumns = ['id', 'type', 'action'];
        $response = request_get($path, ['access_token' => 'token', 'fields' => $someColumns]);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $someColumns
        ]);

        // Selecting some columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $someColumns)]);
        assert_response($this, $response, [
            'data' => 'array',
            'fields' => $someColumns
        ]);
    }

    public function testMeta()
    {
        $path = 'activities';
        $response = request_get($path, [
            'meta' => '*',
            'access_token' => 'token'
        ]);

        assert_response($this, $response, [
            'data' => 'array'
        ]);
        assert_response_meta($this, $response, [
            'table' => 'directus_activity',
            'type' => 'collection'
        ]);
    }

    public function testLimit()
    {
        $path = 'activities';
        $response = request_get($path, [
            'meta' => '*',
            'access_token' => 'token',
            'limit' => 10
        ]);

        assert_response($this, $response, [
            'count' => 10,
            'data' => 'array'
        ]);
        assert_response_meta($this, $response, [
            'table' => 'directus_activity',
            'type' => 'collection',
            'result_count' => 10
        ]);
    }

    public function testId()
    {
        $path = 'activities';
        $response = request_get($path, [
            'meta' => '*',
            'access_token' => 'token',
            'id' => 1
        ]);

        assert_response($this, $response);
        assert_response_meta($this, $response, [
            'table' => 'directus_activity',
            'type' => 'item'
        ]);
    }

    public function testActivity()
    {
        $this->truncateTable();

        // Authenticate
        request_post('auth/authenticate', [
            'email' => 'admin@getdirectus.com',
            'password' => 'password'
        ]);

        request_post('items/test', [
            'name' => 'Product 1'
        ], ['query' => ['access_token' => 'token']]);

        request_patch('items/test/1', [
            'name' => 'Product 01'
        ], ['query' => ['access_token' => 'token']]);

        request_delete('items/test/1', ['query' => ['access_token' => 'token']]);

        $response = request_get('activities', ['access_token' => 'token']);

        assert_response($this, $response, [
            'data' => 'array',
            'count' => 4
        ]);

        $result = response_to_object($response);
        $data = $result->data;
        $actions = [
            DirectusActivityTableGateway::ACTION_LOGIN,
            DirectusActivityTableGateway::ACTION_ADD,
            DirectusActivityTableGateway::ACTION_UPDATE,
            DirectusActivityTableGateway::ACTION_DELETE
        ];

        foreach ($data as $item) {
            $this->assertSame(array_shift($actions), $item->action);
        }
    }

    public function testGetActivity()
    {
        $response = request_get('activities/1', ['access_token' => 'token']);
        assert_response($this, $response);

        $this->truncateTable();

        try {
            $response = request_get('activities/1', ['access_token' => 'token']);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        assert_response_error($this, $response, [
            'status' => 404,
            'code' => ItemNotFoundException::ERROR_CODE
        ]);
    }

    protected function truncateTable()
    {
        if ($this->db) {
            $this->db->execute('TRUNCATE directus_activity;');
        }
    }

    protected function createSampleTables()
    {
        if (!$this->db) {
            return;
        }

        $query = 'CREATE TABLE `test` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

        $this->db->execute($query);
    }

    protected function dropSampleTables()
    {
        if (!$this->db) {
            return;
        }

        $query = 'DROP TABLE `test`;';
        $this->db->execute($query);
    }
}
