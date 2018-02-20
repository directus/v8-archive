<?php


use Phinx\Migration\AbstractMigration;

class CreateGroupsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('directus_groups');

        $table->addColumn('name', 'string', [
            'limit' => 100,
            'null' => false
        ]);
        $table->addColumn('description', 'string', [
            'limit' => 500,
            'null' => true,
            'default' => NULL
        ]);
        $table->addColumn('ip_whitelist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('nav_blacklist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('nav_override', 'text', [
            'null' => true,
            'default' => null
        ]);

        $table->addIndex('name', [
            'unique' => true,
            'name' => 'idx_group_name'
        ]);

        $table->create();
    }
}
