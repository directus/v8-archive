<?php


use Phinx\Migration\AbstractMigration;

class CreatePermissionsTable extends AbstractMigration
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
        $table = $this->table('directus_permissions');

        $table->addColumn('collection', 'string', [
            'limit' => 64,
            'null' => false,
        ]);
        $table->addColumn('group', 'integer', [
            'signed' => false,
            'null' => false
        ]);
        $table->addColumn('status', 'integer', [
            'default' => null,
            'null' => true
        ]);
        $table->addColumn('create', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false,
        ]);
        $table->addColumn('read', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false,
        ]);
        $table->addColumn('update', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false,
        ]);
        $table->addColumn('delete', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false,
        ]);
        $table->addColumn('navigate', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false,
        ]);
        $table->addColumn('explain', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false
        ]);
        $table->addColumn('read_field_blacklist', 'string', [
            'limit' => 1000,
            'null' => true,
            'default' => null,
            'encoding' => 'utf8'
        ]);
        $table->addColumn('write_field_blacklist', 'string', [
            'limit' => 1000,
            'null' => true,
            'default' => NULL,
            'encoding' => 'utf8',
        ]);

        $table->create();
    }
}
