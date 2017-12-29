<?php

namespace Directus\Tests\Api\Io;

class ActivitiesTest extends \PHPUnit_Framework_TestCase
{
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
        response_assert($this, $response);
        response_assert_fields($this, $response, $columns);

        // Using Asterisk
        $response = request_get($path, ['access_token' => 'token', 'fields' => '*']);
        response_assert($this, $response);
        response_assert_fields($this, $response, $columns);

        // Using a list of columns (array)
        $response = request_get($path, ['access_token' => 'token', 'fields' => $columns]);
        response_assert($this, $response);
        response_assert_fields($this, $response, $columns);

        // Using a list of columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $columns)]);
        response_assert($this, $response);
        response_assert_fields($this, $response, $columns);

        // Selecting some columns (array)
        $someColumns = ['id', 'type', 'action'];
        $response = request_get($path, ['access_token' => 'token', 'fields' => $someColumns]);
        response_assert($this, $response);
        response_assert_fields($this, $response, $someColumns);

        // Selecting some columns (csv)
        $response = request_get($path, ['access_token' => 'token', 'fields' => implode(',', $someColumns)]);
        response_assert($this, $response);
        response_assert_fields($this, $response, $someColumns);
    }

    public function testMeta()
    {
        $path = 'activities';
        $response = request_get($path, [
            'meta' => '*',
            'access_token' => 'token'
        ]);

        response_assert($this, $response);
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

        response_assert($this, $response, ['count' => 10]);
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
