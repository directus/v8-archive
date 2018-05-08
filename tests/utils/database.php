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
 * Checks whether a given column exists in a table
 *
 * @param Connection $db
 * @param $table
 * @param $column
 *
 * @return bool
 */
function column_exists(Connection $db, $table, $column)
{
    $query = 'SHOW COLUMNS IN `%s` LIKE "%s";';

    $result = $db->execute(sprintf($query, $table, $column));

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

function table_insert(Connection $db, $table, array $data)
{
    $query = 'INSERT INTO `%s` (%s) VALUES (%s)';

    $columns = array_map(function ($column) {
        return sprintf('`%s`', $column);
    }, array_keys($data));

    $values = array_map(function ($value) {
        if (is_string($value)) {
            $value = sprintf('"%s"', $value);
        } else if (is_null($value)) {
            $value = 'NULL';
        }

        return $value;
    }, $data);

    $db->execute(sprintf($query, $table, implode(', ', $columns), implode(', ', $values)));
}

/**
 * @param Connection $db
 * @param string $table
 */
function drop_table(Connection $db, $table)
{
    $query = 'DROP TABLE IF EXISTS `%s`;';
    $db->execute(sprintf($query, $table));

    delete_item($db, 'directus_collections', [
        'collection' => $table
    ]);
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

/**
 * Resets a table to a given id
 *
 * Removes every item after the $nextId
 *
 * @param Connection $db
 * @param $table
 * @param $nextId
 */
function reset_table_id(Connection $db, $table, $nextId)
{
    $deleteQueryFormat = 'DELETE FROM `%s` WHERE `id` >= %d;';
    $db->execute(sprintf(
        $deleteQueryFormat,
        $table,
        $nextId
    ));

    reset_autoincrement($db, $table, $nextId);
}
