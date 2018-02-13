<?php

namespace Directus\Tests\Api\Io;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $response = request_get('ping', [], [
            'env' => false
        ]);
        assert_response_contents($this, $response, 'pong', [
            'status' => 200
        ]);
    }

    public function testErrorExtraInformation()
    {
        // TODO: Switch between production and development to add more error information
    }
}
