<?php

use Directus\Database\Connection;

/**
 * Creates a new connection instance
 *
 * TODO: Accept parameters
 * TODO: Get this info from env/global
 *
 * @return Connection
 */
function create_db_connection()
{
    $charset = 'utf8mb4';

    return new \Directus\Database\Connection([
        'driver' => 'Pdo_mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'directus_test',
        'username' => 'root',
        'password' => null,
        'charset' => $charset,
        \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        \PDO::MYSQL_ATTR_INIT_COMMAND => sprintf('SET NAMES "%s"', $charset)
    ]);
}

/**
 * Fill a table with a array of key values
 *
 * @param Connection $db
 * @param string $table
 * @param array $items
 */
function fill_table(Connection $db, $table, array $items)
{
    $gateway = new \Zend\Db\TableGateway\TableGateway($table, $db);

    foreach ($items as $item) {
        $gateway->insert($item);
    }
}

/**
 * @param Connection $db
 * @param string $table
 */
function truncate_table(Connection $db, $table)
{
    $query = 'TRUNCATE `%s`;';
    $db->execute(sprintf($query, $table));
}

/**
 * @param Connection $db
 * @param string $table
 * @param int $value
 */
function reset_autoincrement(Connection $db, $table, $value = 1)
{
    $query = 'ALTER TABLE `%s` AUTO_INCREMENT = %d;';

    $db->execute(sprintf($query, $table, $value));
}
