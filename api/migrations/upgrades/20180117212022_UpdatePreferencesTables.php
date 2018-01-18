<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class UpdatePreferencesTables extends Ruckusing_Migration_Base
{
    protected $tableName = 'directus_preferences';

    public function up()
    {
        $this->updateSortColumns();

        if ($this->has_column($this->tableName, 'user')) {
            $this->change_column($this->tableName, 'user', 'integer', [
                'unsigned' => true,
                'null' => false
            ]);
        }

        if ($this->has_column($this->tableName, 'table_name')) {
            $this->change_column($this->tableName, 'table_name', 'string', [
                'limit' => 64,
                'null' => false
            ]);
        }

        if ($this->has_column($this->tableName, 'columns_visible')) {
            $this->rename_column($this->tableName, 'columns_visible', 'visible_fields');
        }

        if ($this->has_column($this->tableName, 'sort')) {
            $this->change_column($this->tableName, 'sort', 'string', [
                'limit' => 64,
                'default' => null
            ]);
        }

        if ($this->has_column($this->tableName, 'sort_order')) {
            $this->remove_column($this->tableName, 'sort_order');
        }

        if ($this->has_column($this->tableName, 'status')) {
            $this->change_column($this->tableName, 'status', 'string', [
                'limit' => 64,
                'default' => null
            ]);
        }
    }//up()

    public function down()
    {
        if (!$this->has_column($this->tableName, 'sort_order')) {
            $this->add_column($this->tableName, 'sort_order', 'string', [
                'limit' => 5,
                'default' => 'ASC'
            ]);
        }

        $this->updateSortColumnsToOldFormat();
    }//down()

    protected function updateSortColumns()
    {
        $query = 'SELECT `id`, `sort`, `sort_order`';
        $query.= sprintf(' FROM `%s`', $this->tableName);
        $results = $this->execute($query);

        $updateQuery = 'UPDATE `%s` SET `sort` = "-%s" WHERE `id` = %s';
        foreach ($results as $row) {
            $id = $row['id'];
            $order = $row['sort_order'];
            $column = $row['sort'];

            if ($order === 'DESC' && strpos($column, '-') !== 0) {
                $this->execute(sprintf($updateQuery, $this->tableName, $column, $id));
            }
        }
    }

    protected function updateSortColumnsToOldFormat()
    {
        if (!$this->has_column($this->tableName, 'sort_order')) {
            return;
        }

        $query = 'SELECT `id`, `sort`';
        $query.= sprintf(' FROM `%s`', $this->tableName);
        $results = $this->execute($query);

        $updateQuery = 'UPDATE `%s` SET `sort_order` = "%s" WHERE `id` = %s';
        foreach ($results as $row) {
            $id = $row['id'];
            $column = $row['sort'];
            $order = strpos($column, '-') === 0 ? 'DESC': 'ASC';

            $this->execute(sprintf($updateQuery, $this->tableName, $order, $id));
        }
    }
}
