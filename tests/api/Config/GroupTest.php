<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Source;
use Directus\Config\Schema\Group;
use Directus\Config\Schema\Value;
use Directus\Config\Schema\Types;

class GroupTest extends \PHPUnit\Framework\TestCase
{
    public function testGroup()
    {
        $group = new Group("GrOuP", []);

        // Key should be upper cased
        $this->assertEquals("group", $group->key());

        // Name should remain the same
        $this->assertEquals("GrOuP", $group->name());

        // Optional should be false
        $this->assertEquals(false, $group->optional());

        $group = new Group("Some_Group?", []);

        // Key should remove underscores
        $this->assertEquals("somegroup", $group->key());

        // Name should remain the same
        $this->assertEquals("Some_Group", $group->name());

        // Optional should be true because of question mark
        $this->assertTrue($group->optional());
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
