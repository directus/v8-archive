<?php

class ConnectionTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $mockDriver = get_mock_driver($this);
        $connection = new \Directus\Database\Connection($mockDriver);

        $adapterConnection = $connection->getDriver()->getConnection();
        $adapterConnection->expects($this->once())
            ->method('connect');

        $connection->connect();
    }

    public function testStrictMode()
    {
        // $connection = get_mock_connection($this, [
        //     'result_data' => ['modes' => 'NOTHING,HERE']
        // ]);

        $mockDriver = get_mock_driver($this, [
            'result_data' => ['modes' => 'NOTHING,HERE']
        ]);
        $connection = new \Directus\Database\Connection($mockDriver);

        $this->assertFalse($connection->isStrictModeEnabled());

        // $connection = get_mock_connection($this, [
        //     'platform_name' => 'sqlite'
        // ]);
        $mockDriver = get_mock_driver($this, [
            'platform_name' => 'sqlite'
        ]);
        $connection = new \Directus\Database\Connection($mockDriver);

        $this->assertFalse($connection->isStrictModeEnabled());

        // $connection = get_mock_connection($this, [
        //     'platform_name' => 'mysql',
        //     'result_data' => ['modes' => 'STRICT_ALL_TABLES,NOTHING']
        // ]);

        $mockDriver = get_mock_driver($this, [
            'platform_name' => 'mysql',
            'result_data' => ['modes' => 'STRICT_ALL_TABLES,NOTHING']
        ]);
        $connection = new \Directus\Database\Connection($mockDriver);

        $this->assertTrue($connection->isStrictModeEnabled());
    }
}
