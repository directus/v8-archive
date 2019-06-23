<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Context;
use Directus\Config\Schema\Group;
use Directus\Config\Schema\Value;
use Directus\Config\Schema\Types;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    public function testValue()
    {
        $context = Context::from_array([
            "group" => [
                "value" => "12345"
            ]
        ]);

        $group = new Group("group", [
            new Value("value", Types::INTEGER)
        ]);

        $value = $group->value($context);

        $this->assertInternalType("int", $value["value"]);
    }
}
