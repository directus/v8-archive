<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Connection;

class ActivitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $db;

    public function setUp()
    {
        $this->db = create_db_connection();

        $this->db->execute('TRUNCATE directus_activity;');

        $startDay = date('d') - 15;
        for ($i = 0; $i < 15; $i++) {
            $datetime = sprintf('2017-12-26 15:52:37', $startDay);
            $query = "INSERT INTO `directus_activity` (`id`, `type`, `action`, `identifier`, `table_name`, `row_id`, `user`, `data`, `delta`, `parent_id`, `parent_table`, `parent_changed`, `datetime`, `logged_ip`, `user_agent`)";
            $query.= "VALUES (DEFAULT, 'LOGIN', 'LOGIN', NULL, 'directus_users', 0, 1, NULL, NULL, NULL, NULL, 0, '" . $datetime . "', '::1', 'GuzzleHttp/6.2.1 curl/7.52.1 PHP/5.5.38');";

            $startDay++;
            $this->db->execute($query);
        }
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
        response_assert($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using Asterisk
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*']);
        response_assert($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using a list of columns (array)
        $response = request_get($path, ['access_token' => 'token', 'fields' => $columns]);
        response_assert($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Using a list of columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $columns)]);
        response_assert($this, $response, [
            'data' => 'array',
            'fields' => $columns
        ]);

        // Selecting some columns (array)
        $someColumns = ['id', 'type', 'action'];
        $response = request_get($path, ['access_token' => 'token', 'fields' => $someColumns]);
        response_assert($this, $response, [
            'data' => 'array',
            'fields' => $someColumns
        ]);

        // Selecting some columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $someColumns)]);
        response_assert($this, $response, [
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

        response_assert($this, $response, [
            'data' => 'array'
        ]);
        response_assert_meta($this, $response, [
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

        response_assert($this, $response, [
            'count' => 10,
            'data' => 'array'
        ]);
        response_assert_meta($this, $response, [
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

        response_assert($this, $response);
        response_assert_meta($this, $response, [
            'table' => 'directus_activity',
            'type' => 'item'
        ]);
    }
}
