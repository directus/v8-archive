<?php

$loader = require __DIR__ . '/vendor/autoload.php';
// utils for I/O testing
require __DIR__ . '/tests/utils/io_functions.php';

define_constant('BASE_PATH', __DIR__);
define_constant('STATUS_COLUMN_NAME', 'active');

// force a timezone
date_default_timezone_set('America/New_York');

/**
 * @param $testCase
 * @param $className
 * @param array|null $methods
 * @param array|null $arguments
 *
 * @return PHPUnit_Framework_MockObject_MockObject
 */
function create_mock($testCase, $className, array $methods = null, array $arguments = null)
{
    $builder = new PHPUnit_Framework_MockObject_MockBuilder($testCase, $className);

    if (is_array($methods) && count($methods) > 0) {
        $builder->setMethods($methods);
    }

    if (is_array($arguments) && count($arguments) > 0) {
        $builder->setConstructorArgs($arguments);
    }

    return $builder->disableOriginalConstructor()
                    ->disableOriginalClone()
                    ->disableArgumentCloning()
                    ->disallowMockingUnknownTypes()
                    ->getMock();
}

/**
 * @param \PHPUnit\Framework\TestCase $testCase
 * @param $attributes - mock attributes
 *
 * @return \Zend\Db\Adapter\Adapter
 */
function get_mock_adapter($testCase, $attributes = [])
{
    $mockDriver = get_mock_driver($testCase, $attributes);

    // setup mock adapter
    $methods = ['getDriver', 'getConnection', 'getCurrentSchema', 'getPlatform'];
    $mockAdapter = create_mock($testCase, 'Zend\Db\Adapter\Adapter', $methods, [$mockDriver]);
    $mockAdapter->expects($testCase->any())->method('getDriver')->will($testCase->returnValue($mockDriver));
    $mockAdapter->expects($testCase->any())->method('getConnection')->will($testCase->returnValue(get_mock_adapter_connection($testCase)));
    $mockAdapter->expects($testCase->any())->method('getCurrentSchema')->will($testCase->returnValue('directus'));
    $mockAdapter->expects($testCase->any())->method('getPlatform')->will($testCase->returnValue(get_mock_platform($testCase)));

    return $mockAdapter;
}

/**
 * @param PHPUnit_Framework_TestCase $testCase
 * @param array $attributes
 *
 * @return PHPUnit_Framework_MockObject_MockObject
 */
function get_mock_connection($testCase, $attributes = [])
{
    $mockDriver = get_mock_driver($testCase, $attributes);

    // setup mock connection adapter
    $mockConnectionAdapter = create_mock($testCase, '\Directus\Database\Connection', null, [$mockDriver]);

    $platform = get_mock_platform($testCase, $attributes);

    $mockConnectionAdapter->expects($testCase->any())->method('getPlatform')->will($testCase->returnValue($platform));
    $mockConnectionAdapter->expects($testCase->any())->method('getDriver')->will($testCase->returnValue($mockDriver));

    return $mockConnectionAdapter;
}

function get_connection($test)
{

}

function get_mock_platform($testCase, array $attributes = [])
{
    // Platform
    $platformName = isset($attributes['platform_name']) ? $attributes['platform_name'] : 'Mysql';
    $platform = create_mock($testCase, \Zend\Db\Adapter\Platform\Mysql::class);
    $platform->expects($testCase->any())->method('getIdentifierSeparator')->will($testCase->returnValue('.'));
    $platform->expects($testCase->any())->method('getName')->will($testCase->returnValue($platformName));

    return $platform;
}

function get_mock_adapter_connection($testCase)
{
    $mockConnection = create_mock($testCase, 'Zend\Db\Adapter\Driver\ConnectionInterface');
    $mockConnection->expects($testCase->any())->method('getCurrentSchema')->will($testCase->returnValue('directus_schema'));
    $mockConnection->expects($testCase->any())->method('connect');
    // $mockConnection->expects($testCase->any())->method('getDatabasePlatformName')->will($testCase->returnValue($platformName));
    // $mockConnection->expects($testCase->any())->method('getDriverName')->will($testCase->returnValue($platformName));

    return $mockConnection;
}

/**
 * @param PHPUnit_Framework_TestCase  $testCase
 * @param array $attributes
 *
 * @return PHPUnit_Framework_MockObject_MockObject
 */
function get_mock_driver($testCase, $attributes = [])
{
    $resultCount = 0;
    if (isset($attributes['result_count'])) {
        $resultCount = (int)$attributes['result_count'];
    }

    $resultData = null;
    if (isset($attributes['result_data'])) {
        $resultData = $attributes['result_data'];
    }

    $platformName = 'Mysql';
    if (isset($attributes['platform_name'])) {
        $platformName = $attributes['platform_name'];
    }

    // mock the adapter, driver, and parts
    $mockResult = create_mock($testCase, 'Zend\Db\Adapter\Driver\ResultInterface');
    $mockResult->expects($testCase->any())->method('count')->will($testCase->returnValue($resultCount));
    $mockResult->expects($testCase->any())->method('current')->will($testCase->returnValue($resultData));
    $mockStatement = create_mock($testCase, 'Zend\Db\Adapter\Driver\StatementInterface');
    $mockStatement->expects($testCase->any())->method('execute')->will($testCase->returnValue($mockResult));

    // Connection
    $mockAdapterConnection = get_mock_adapter_connection($testCase);

    $mockDriver = create_mock($testCase,'Zend\Db\Adapter\Driver\DriverInterface');
    $mockDriver->expects($testCase->any())->method('createStatement')->will($testCase->returnValue($mockStatement));
    $mockDriver->expects($testCase->any())->method('getConnection')->will($testCase->returnValue($mockAdapterConnection));
    $mockDriver->expects($testCase->any())->method('getDatabasePlatformName')->will($testCase->returnValue($platformName));

    return $mockDriver;
}

function get_mysql_schema($testCase, $attributes = [])
{
    $mockAdapter = get_mock_adapter($testCase, $attributes);

    return new \Directus\Database\Schemas\Sources\MySQLSchema($mockAdapter);
}

/**
 * @param \PHPUnit\Framework\TestCase $testCase
 * @param array $methods
 *
 * @return mixed
 */
function get_mock_mysql_schema($testCase, $methods = [])
{
    $mockAdapter = get_mock_adapter($testCase);
    $mockSchema = $testCase->getMockBuilder('\Directus\Database\Schemas\Sources\MySQLSchema')
        ->setConstructorArgs([$mockAdapter])
        ->setMethods($methods)
        ->getMock();

    return $mockSchema;
}

function get_array_session()
{
    $storage = new \Directus\Session\Storage\ArraySessionStorage();

    return new \Directus\Session\Session($storage);
}

/**
 * @param string $url
 * @param array $params
 *
 * @return string
 */
function add_query_params($url, array $params)
{
    $urlParts = parse_url($url);
    if (is_array($urlParts) && isset($urlParts['query'])) {
        $urlParts['query'] = array_merge($urlParts['query'], $params);
    }

    return unparse_url($urlParts);
}
