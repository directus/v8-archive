<?php

namespace Directus\Tests\Api;

class ExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testSomething()
    {
        $url = 'http://localhost:9000/api/1.1/ping';
        $options = array(
            'http' => array(
                'method'  => 'GET',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n"
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $this->assertSame('pong', $result);
    }
}
