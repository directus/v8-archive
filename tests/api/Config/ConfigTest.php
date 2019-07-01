<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\Context;
use Directus\Config\Schema\Schema;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testItem()
    {
        // This is the old way to load config: we require the api.php file from disk
        // and pass the result value into the Config constructor
        $config = new Config([
            "option" => 1
        ]);

        $this->assertSame(1, $config->get("option"));
    }
    
    public function testItemsUsingSchema()
    {
        // Get the configuration schema
        $schema = Schema::get();

        // Load context from somewhere (file, env, json, etc.)
        $context = Context::from_file(__DIR__ . "/../../../config/api_sample.php");

        // Load context into schema to get normalized/default/converted values
        $values = $schema->value([
            // We pass the context inside a "directus" key because it's the root group
            // because it makes every environment variable begin with DIRECTUS_ prefix
            "directus" => $context
        ]);

        // Pass values to the Config class exactly as it is today
        $config = new Config($values);

        // Fetch a value from config
        $this->assertEquals("admin@example.com", $config->get("mail.default.from"));
    }
    
    public function testSchemaUsingEnvironement()
    {
        // Simulate an environment variable
        $_ENV["DIRECTUS_MAIL_DEFAULT_FROM"] = "wolfulus@directus.com";

        // Get the configuration schema
        $schema = Schema::get();
        $context = Context::from_env();

        // We don't need to put it inside directus key in env
        $values = $schema->value($context); 

        // Fetch values
        $config = new Config($values);
        $this->assertEquals("wolfulus@directus.com", $config->get("mail.default.from"));
    }
}
