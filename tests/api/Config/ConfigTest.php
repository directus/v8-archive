<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Source;
use Directus\Config\Schema\Group;
use Directus\Config\Schema\Value;
use Directus\Config\Schema\Types;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testItem()
    {
        $config = new Config([
            'option' => 1
        ]);

        $this->assertSame(1, $config->get('option'));
    }

    public function testSourceMap()
    {
        $source = Source::map([
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

        $source = Source::from_env();

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

        $this->assertArraySubset($expected, $source);
    }

    public function testContextFile()
    {
        $context = Source::from_php(__DIR__ . "/sources/source.php");

        $expected = [
            "hello" => [
                "world" => [
                    "a" => "1",
                    "b" => "2"
                ],
            ],
        ];

        $this->assertEquals($expected, $context);
    }

    /*
    public function testSchemaNodes()
    {
        putenv("PREFIX_SOME_DEEP_BOOL=true");
        putenv("PREFIX_SOME_SUPER_DEEP_INT=1234");

        $context = Context::from_env("PREFIX");

        $schema = new Group('some', [
            new Group('deep', [
                new Value('bool', Types::BOOLEAN),
            ]),
            new Group('super', [
                new Group('deep', [
                    new Value('int', Types::INTEGER),
                ]),
            ]),
        ]);
    }
    */

}
