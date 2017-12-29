<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ForbiddenSystemTableDirectAccessException;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    protected $systemTables = [
        'directus_activity',
        'directus_bookmarks',
        'directus_columns',
        'directus_files',
        'directus_groups',
        'directus_messages',
        'directus_messages_recipients',
        'directus_preferences',
        'directus_privileges',
        'directus_settings',
        'directus_tables',
        'directus_users'
    ];

    public function testNotDirectAccess()
    {
        // Fetching items
        foreach ($this->systemTables as $table) {
            $path = 'items/' . $table;
            $response = request_error_get($path);

            response_assert_error($this, $response, [
                'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                'status' => 401
            ]);
        }

        // Creating Item
        foreach ($this->systemTables as $table) {
            $path = 'items/' . $table;
            $response = request_error_post($path);

            response_assert_error($this, $response, [
                'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                'status' => 401
            ]);
        }

        // Fetching a Item
        foreach ($this->systemTables as $table) {
            foreach (['GET', 'PATCH', 'PUT', 'DELETE'] as $method) {
                $path = 'items/' . $table . '/1';
                $response = call_user_func('request_error_' . strtolower($method), $path);

                response_assert_error($this, $response, [
                    'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                    'status' => 401
                ]);
            }
        }

        // Fetching a Item
        foreach ($this->systemTables as $table) {
            foreach (['POST', 'PATCH', 'PUT', 'DELETE'] as $method) {
                $path = 'items/' . $table . '/batch';
                $response = call_user_func('request_error_' . strtolower($method), $path);

                response_assert_error($this, $response, [
                    'code' => ForbiddenSystemTableDirectAccessException::ERROR_CODE,
                    'status' => 401
                ]);
            }
        }
    }
}
