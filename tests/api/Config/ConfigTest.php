<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testItem()
    {
        $config = new Config([
            'option' => 1
        ]);

        $this->assertSame(1, $config->get('option'));
    }
}
