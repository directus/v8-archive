<?php

namespace Directus\Tests\Core;

use Directus\Core\Options\Options;
use Directus\Core\Options\Exception\EmptySchema;
use PHPUnit\Framework\TestCase;

/**
 * Options tests.
 */
final class OptionsTest extends TestCase
{
    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testFailToCreateWithEmptySchema()
    {
        $this->expectException(EmptySchema::class);
        new Options([]);
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testCreateWithStringProps()
    {
        $options = new Options(['var1', 'var2'], [
            'var1' => 'hello',
            'var2' => 'world',
        ]);

        static::assertEquals($options->get('var1'), 'hello');
        static::assertEquals($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testCreateWithComplexProps()
    {
        $options = new Options([
            'var1' => [],
            'var2' => [],
        ], [
            'var1' => 'hello',
            'var2' => 'world',
        ]);

        static::assertEquals($options->get('var1'), 'hello');
        static::assertEquals($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testCreateWithMixedProps()
    {
        $options = new Options([
            'var1',
            'var2' => [],
        ], [
            'var1' => 'hello',
            'var2' => 'world',
        ]);

        static::assertEquals($options->get('var1'), 'hello');
        static::assertEquals($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testOptionalParameters()
    {
        $options = new Options([
            'var1',
            'var2' => [
                'default' => 'worldssss',
            ],
        ], [
            'var1' => 'hello',
        ]);

        static::assertEquals($options->get('var1'), 'hello');
        static::assertEquals($options->get('var2'), 'worldssss');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::__construct
     */
    public function testOptionalImmediateDefaultParameters()
    {
        $options = new Options([
            'var1' => 12345,
            'var2' => 'default_value',
        ], []);

        static::assertEquals($options->get('var1'), 12345);
        static::assertEquals($options->get('var2'), 'default_value');
    }
}
