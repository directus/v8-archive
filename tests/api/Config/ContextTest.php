<?php

namespace Directus\Tests\Config;

use Directus\Config\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testSourceMap()
    {
        $source = Context::from_map([
            "HELLO_WORLD_1" => "value1",
            "HELLO_WORLD_2" => "value2",
        ]);

        $expected = [
            "hello" => [
                "world" => [
                    "value1",
                    "value2"
                ],
            ],
        ];

        // Should map keys to complex objects
        $this->assertEquals($expected, $source);
    }

    public function testOverwrites()
    {
        $source = Context::from_map([
            "HELLO" => "value1",
            "HELLO_WORLD" => "value2",
        ]);

        $expected = [
            "hello" => [
                "world" => "value2",
            ],
        ];

        // Bigger keys wins the trade and should overwrite already set values
        $this->assertEquals($expected, $source);

        $source = Context::from_map([
            "HELLO_WORLD" => "value2",
            "HELLO" => "value1",
        ]);

        // Array order should not be a problem
        $this->assertEquals($expected, $source);
    }

    public function testSourceEnv()
    {
        $_ENV['HELLO_WORLD_A'] = "1";
        $_ENV['HELLO_WORLD_B'] = "2";
        $_ENV['HELLO_ARRAY_10_A'] = "3";
        $_ENV['HELLO_ARRAY_10_B'] = "4";
        $_ENV['HELLO_ARRAY_15_A'] = "5";
        $_ENV['HELLO_ARRAY_15_B'] = "6";

        $source = Context::from_env();

        $expected = [
            "hello" => [
                "world" => [
                    "a" => "1",
                    "b" => "2",
                ],
                "array" => [
                    [
                        "a" => "3",
                        "b" => "4",
                    ],
                    [
                        "a" => "5",
                        "b" => "6",
                    ]
                ]
            ],
        ];

        // Should read values from environment variables
        $this->assertArraySubset($expected, $source);
    }

    public function testArray()
    {
        $context = Context::from_array([
            "hello" => [
                "world" => [
                    "a" => "1",
                    "b" => "2"
                ],
            ],
        ]);

        $expected = [
            "hello" => [
                "world" => [
                    "a" => "1",
                    "b" => "2"
                ],
            ],
        ];

        // Should load values from php associative array
        $this->assertEquals($expected, $context);
    }

    public function testContextFile()
    {
        $context = Context::from_php(__DIR__ . "/sources/source.php");

        $expected = [
            "hello" => [
                "world" => [
                    "a" => "1",
                    "b" => "2"
                ],
            ],
        ];

        // Should load values from php source file
        $this->assertEquals($expected, $context);
    }
}
