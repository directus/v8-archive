<?php

namespace Directus\Tests\Api;

use Directus\Container\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testContainer()
    {
        $container = new Container(['name' => 'john']);

        $this->assertSame('john', $container->get('name'));
        $this->assertTrue($container->has('name'));

        $container->set('age', 10);
        $this->assertSame(10, $container->get('age'));

        $container->set('country', function () {
            return 'us';
        });

        $this->assertSame('us', $container->get('country'));
    }

    /**
     * @expectedException \Directus\Container\Exception\ValueNotFoundException
     */
    public function testNotFoundValue()
    {
        $container = new Container();

        $name = $container->get('name');
    }
}
