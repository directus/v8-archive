<?php

namespace Directus\Tests\Api\Io;

class ActivitySkipTest extends \PHPUnit\Framework\TestCase
{
    protected static $db;

    public static function resetData()
    {
        truncate_table(static::$db, 'directus_activity');
    }

    public static function setUpBeforeClass()
    {
        static::$db = create_db_connection();
        static::resetData();
    }

    public static function tearDownAfterClass()
    {
        static::resetData();
    }

    public function testSkipActivity()
    {
        $this->assertSame(0, table_count(static::$db, 'directus_activity'));
        $path = 'items/products';
        $response = request_post(
            $path,
            ['name' => 'product', 'price' => 10.00],
            ['query' => ['access_token' => 'token', 'activity_skip' => true]]
        );

        $this->assertSame(0, table_count(static::$db, 'directus_activity'));

        $product = response_get_data($response);
        $response = request_patch(
            $path . '/' . $product->id,
            ['name' => 'product 2'],
            ['query' => ['access_token' => 'token', 'activity_skip' => true]]
        );

        $this->assertSame(0, table_count(static::$db, 'directus_activity'));

        $response = request_delete(
            $path . '/' . $product->id,
            ['query' => ['access_token' => 'token', 'activity_skip' => true]]
        );

        $this->assertSame(0, table_count(static::$db, 'directus_activity'));
    }

    public function testSkipActivityOff()
    {
        $this->assertSame(0, table_count(static::$db, 'directus_activity'));
        $path = 'items/products';
        $response = request_post(
            $path,
            ['name' => 'product', 'price' => 10.00],
            ['query' => ['access_token' => 'token', 'activity_skip' => false]]
        );

        $this->assertSame(1, table_count(static::$db, 'directus_activity'));

        $product = response_get_data($response);
        $response = request_patch(
            $path . '/' . $product->id,
            ['name' => 'product 2'],
            ['query' => ['access_token' => 'token', 'activity_skip' => false]]
        );

        $this->assertSame(2, table_count(static::$db, 'directus_activity'));

        $response = request_delete(
            $path . '/' . $product->id,
            ['query' => ['access_token' => 'token', 'activity_skip' => false]]
        );

        $this->assertSame(3, table_count(static::$db, 'directus_activity'));
    }

    public function testWithoutSkipActivity()
    {
        $this->assertSame(3, table_count(static::$db, 'directus_activity'));
        $path = 'items/products';
        $response = request_post(
            $path,
            ['name' => 'product', 'price' => 10.00],
            ['query' => ['access_token' => 'token']]
        );

        $this->assertSame(4, table_count(static::$db, 'directus_activity'));

        $product = response_get_data($response);
        $response = request_patch(
            $path . '/' . $product->id,
            ['name' => 'product 2'],
            ['query' => ['access_token' => 'token']]
        );

        $this->assertSame(5, table_count(static::$db, 'directus_activity'));

        $response = request_delete(
            $path . '/' . $product->id,
            ['query' => ['access_token' => 'token']]
        );

        $this->assertSame(6, table_count(static::$db, 'directus_activity'));
    }
}
