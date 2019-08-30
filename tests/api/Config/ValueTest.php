<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Context;
use Directus\Config\Schema\Group;
use Directus\Config\Schema\Value;
use Directus\Config\Schema\Types;

class ValueTest extends \PHPUnit\Framework\TestCase
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

    public function testOptionalValue()
    {
        $context = Context::from_array([
            "group" => [
                "optional" => "2222"
            ]
        ]);

        $group = new Group("group", [
            new Value("value", Types::INTEGER, 1111),
            new Value("optional?", Types::INTEGER),
            new Value("optional2?", Types::INTEGER)
        ]);

        $values = $group->value($context);

        $this->assertEquals(1111, $values["value"]);
        $this->assertEquals(2222, $values["optional"]);
        $this->assertEquals(null, @$values["optional2"]);
    }
}
