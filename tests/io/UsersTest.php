<?php

namespace Directus\Tests\Api\Io;

use Directus\Database\Exception\ItemNotFoundException;
use Psr\Http\Message\ResponseInterface;

class UsersTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $db = create_db_connection();
        $db->execute('DELETE FROM `directus_users` WHERE `id` > 2;');

        reset_autoincrement($db, 'directus_users', 3);
    }

    public function testCreate()
    {
        $queryParams = ['access_token' => 'token'];
        $path = 'users';

        $email = 'john@getdirectus.com';
        $password = 'password';
        $data = [
            'email' => $email,
            'password' => $password
        ];

        $response = request_post($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'email' => $email
        ]);
        $data = response_get_data($response);
        $this->assertSame($email, $data->email);
    }

    public function testUpdate()
    {
        $queryParams = ['access_token' => 'token'];

        $response = request_get('users/3', $queryParams);
        $currentData = response_get_data($response);

        $email = 'john2@getdirectus.com';
        $password = 'new-password';
        $data = [
            'email' => $email,
            'password' => $password
        ];

        $path = 'users/3';
        $response = request_patch($path, $data, ['query' => $queryParams]);
        assert_response($this, $response);
        assert_response_data_contains($this, $response, [
            'id' => 3,
            'email' => $email
        ]);

        $data = response_get_data($response);

        $this->assertSame($email, $data->email);
    }

    public function testList()
    {
        $queryParams = ['access_token' => 'token'];

        $response = request_get('users', $queryParams);
        assert_response($this, $response, [
            'data' => 'array',
            'count' => 3
        ]);
    }

    public function testGetOne()
    {
        $queryParams = ['access_token' => 'token'];

        $response = request_get('users/3', $queryParams);
        assert_response($this, $response);

        assert_response_data_contains($this, $response, [
            'id' => 3,
            'email' => 'john2@getdirectus.com'
        ]);

        $response = request_error_get('users/4', $queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }

    public function testInvitation()
    {
        // TODO: Make the mailing works again
        // This fails due to the use of Bootstrap
        // $queryParams = ['access_token' => 'token'];
        //
        // $assertInvitationResponse = function (ResponseInterface $response) {
        //     $items = response_get_data($response);
        //     foreach ($items as $item) {
        //         $this->assertNotEmpty($item->invite_token);
        //         $this->assertNotEmpty($item->invite_date);
        //         $this->assertNotEmpty($item->invite_sender);
        //         $this->assertNotEmpty($item->invite_accepted);
        //         $this->assertSame(0, $item->invite_accepted);
        //         $this->assertSame(2, $item->status);
        //     }
        // };
        //
        // $data = ['email' => 'intern@getdirectus.com'];
        // $response = request_post('users/invite', $data, ['query' => $queryParams]);
        // assert_response($this, $response, [
        //     'data' => 'array',
        //     'count' => 1
        // ]);
        //
        // $assertInvitationResponse($response);
        //
        // $data = ['email' => 'intern1@getdirectus.com, intern2@getdirectus.com'];
        // $response = request_post('users/invite', $data, ['query' => $queryParams]);
        // assert_response($this, $response, [
        //     'data' => 'array',
        //     'count' => 2
        // ]);
        //
        // $assertInvitationResponse($response);
    }

    public function testDelete()
    {
        $queryParams = ['access_token' => 'token'];

        $response = request_delete('users/3', ['query' => $queryParams]);
        assert_response_empty($this, $response);

        $response = request_error_get('users/3', $queryParams);
        assert_response_error($this, $response, [
            'code' => ItemNotFoundException::ERROR_CODE,
            'status' => 404
        ]);
    }
}
