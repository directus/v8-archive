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
        $group = new Group("group", [
            new Value("value", Types::INTEGER)
        ]);

        $values = $group->value(Context::from_array([
            "group" => [
                "value" => "12345"
            ]
        ]));

        print_r($values);
    }

    public function testGroupChildren()
    {
        $group = new Group("parent", [
            new Group("child", [])
        ]);

        // Should contain a child
        $this->assertCount(1, $group->children());
    }

    public function testGroupParent()
    {
        $group = new Group("parent", [
            new Group("child", [])
        ]);

        $children = $group->children();

        // Should contain a child
        $this->assertEquals("child", $children[0]->name());
        $this->assertEquals("parent", $children[0]->parent()->name());
    }
}
