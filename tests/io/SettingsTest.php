<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        truncate_table(create_db_connection(), 'directus_settings');
    }

    public function testCreate()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'settings';

        $data = [
            'scope' => 'global',
            'group' => 'app',
            'key' => 'map_source',
            'value' => 'google'
        ];

        $response = request_post($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testUpdate()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'settings/1';

        $data = [
            'value' => 'new-value'
        ];

        $response = request_patch($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, $data);
    }

    public function testList()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'settings';

        $response = request_get($path, $queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 1
        ]);
    }

    public function testGetOne()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'settings/1';

        $response = request_get($path, $queryParams);
        assert_response($this, $response, [
            'has_fields' => true,
            'fields' => ['scope', 'key', 'value']
        ]);
    }

    public function testDelete()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'settings/1';

        $response = request_delete($path, ['query' => $queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get($path, $queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }
}
