<?php

declare(strict_types=1);

namespace Directus\Tests\Core;

use Directus\Core\Options\Exception\EmptySchema;
use Directus\Core\Options\Exception\InvalidOption;
use Directus\Core\Options\Options;
use Directus\Tests\Helpers\DirectusTestCase;

/**
 * Options tests.
 */
final class OptionsTest extends DirectusTestCase
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
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testCreateWithStringProps()
    {
        $options = new Options(['var1', 'var2'], [
            'var1' => 'hello',
            'var2' => 'world',
        ]);

        static::assertSame($options->get('var1'), 'hello');
        static::assertSame($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
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

        static::assertSame($options->get('var1'), 'hello');
        static::assertSame($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
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

        static::assertSame($options->get('var1'), 'hello');
        static::assertSame($options->get('var2'), 'world');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
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

        static::assertSame($options->get('var1'), 'hello');
        static::assertSame($options->get('var2'), 'worldssss');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testOptionalImmediateDefaultParameters()
    {
        $options = new Options([
            'var1' => 12345,
            'var2' => 'default_value',
        ], []);

        static::assertSame($options->get('var1'), 12345);
        static::assertSame($options->get('var2'), 'default_value');
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testDefaultValue()
    {
        $options = new Options([
            'var' => [
                'default' => 'hello',
            ],
        ], []);

        static::assertEquals('hello', $options->get('var'));
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testValueValidation()
    {
        $this->expectException(InvalidOption::class);

        new Options([
            'var' => [
                'validate' => 'is_integer',
            ],
        ], [
            'var' => 'hello',
        ]);
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testValueConversion()
    {
        $options = new Options([
            'var1' => [
                'convert' => 'intval',
            ],
            'var2' => [
                'convert' => 'strval',
            ],
        ], [
            'var1' => '12345',
            'var2' => 12345,
        ]);

        static::assertIsInt($options->get('var1'));
        static::assertIsString($options->get('var2'));
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testDefaultValueConversion()
    {
        $options = new Options([
            'var' => [
                'default' => '12345',
                'convert' => 'intval',
            ],
        ], []);

        static::assertEquals(12345, $options->get('var'));
        static::assertIsInt($options->get('var'));
    }

    /**
     * Test option creation.
     *
     * @covers \Directus\Core\Options\Options::feed
     */
    public function testSubPropertyAccess()
    {
        $options = new Options([
            'parent.child1',
            'parent.child2',
        ], [
            'parent' => [
                'child1' => 1234,
                'child2' => 4321,
            ],
        ]);

        static::assertEquals(1234, $options->get('parent.child1'));
        static::assertEquals(4321, $options->get('parent.child2'));
    }
}
