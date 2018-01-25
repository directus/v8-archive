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
 * @param $table
 *
 * @return bool
 */
function table_exists(Connection $db, $table)
{
    $query = 'SHOW TABLES LIKE "%s";';

    $result = $db->execute(sprintf($query, $table));

    return $result->count() === 1;
}

/**
 * @param Connection $db
 * @param $table
 * @param array $conditions
 *
 * @return array
 */
function table_find(Connection $db, $table, array $conditions)
{
    $gateway = new \Zend\Db\TableGateway\TableGateway($table, $db);
    $result = $gateway->select($conditions);

    return $result->toArray();
}

/**
 * @param Connection $db
 * @param $table
 * @param array $conditions
 *
 * @return int
 */
function delete_item(Connection $db, $table, array $conditions)
{
    $gateway = new \Zend\Db\TableGateway\TableGateway($table, $db);

    return $gateway->delete($conditions);
}

/**
 * @param Connection $db
 * @param string $table
 */
function drop_table(Connection $db, $table)
{
    $query = 'DROP TABLE IF EXISTS `%s`;';
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

function reset_table_id($table, $nextId)
{
    $deleteQueryFormat = 'DELETE FROM `%s` WHERE `id` >= %d;';
    $db = create_db_connection();
    $db->execute(sprintf(
        $deleteQueryFormat,
        $table,
        $nextId
    ));

    reset_autoincrement($db, $table, $nextId);
}
