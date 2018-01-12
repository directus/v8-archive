<?php

namespace Directus\Tests\Api\Io;

use Directus\Exception\UnauthorizedException;
use GuzzleHttp\Exception\ClientException;

class BookmarksTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $data = [
            'title' => 'Directus Docs',
            'url' => 'https://docs.getdirectus.com'
        ];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);

        // =============================================================================

        $data = [
            'title' => 'Directus',
            'url' => 'https://getdirectus.com',
            'user' => 2
        ];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, $data);

        // =============================================================================
        // TEST WITH METADATA
        // =============================================================================

        $data = [
            'title' => 'Directus',
            'url' => 'https://getdirectus.com',
            'user' => 2
        ];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'token', 'meta' => '*']]);
        response_assert($this, $response);
        response_assert_meta($this, $response);
        response_assert_data_contains($this, $response, $data);

        // =============================================================================

        $data = [
            'title' => 'Directus API',
            'url' => 'https://api.getdirectus.com',
            'user' => 1
        ];

        try {
            $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'intern_token']]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        response_assert_error($this, $response, [
            'code' => UnauthorizedException::ERROR_CODE,
            'status' => 401
        ]);

        // =============================================================================

        $data = [
            'title' => 'Directus API',
            'url' => 'https://api.getdirectus.com'
        ];

        $response = request_post('bookmarks', $data, ['query' => ['access_token' => 'intern_token']]);
        response_assert($this, $response);
        response_assert_data_contains($this, $response, [
            'title' => 'Directus API',
            'url' => 'https://api.getdirectus.com',
            'user' => 2
        ]);
    }

    public function testErrorExtraInformation()
    {
        // TODO: Switch between production and development to add more error information
    }
}
