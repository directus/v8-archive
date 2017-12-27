<?php

namespace Directus\Tests\Api\Io;

class GeneralTest extends \PHPUnit_Framework_TestCase
{
    public function testPing()
    {
        $url = 'ping';
        $response = request_get($url);

        $this->assertSame('pong', $response->getBody()->getContents());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testErrorExtraInformation()
    {
        // TODO: Switch between production and development to add more error information
    }
}
