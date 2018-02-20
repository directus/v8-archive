<?php


use Phinx\Migration\AbstractMigration;

class CreateSettingsTable extends AbstractMigration
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
        $table = $this->table('directus_settings');

        $table->addColumn('scope', 'string', [
            'limit' => 64,
            'default' => null
        ]);
        $table->addColumn('group', 'string', [
            'limit' => 64,
            'default' => null
        ]);
        $table->addColumn('key', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('value', 'string', [
            'limit' => 255,
            'default' => null
        ]);

        $table->addIndex(['scope', 'group', 'key'], [
            'unique' => true,
            'name' => 'idx_scope_group_name'
        ]);

        $table->create();
    }
}
