<?php


use Phinx\Migration\AbstractMigration;

class CreateFieldsTable extends AbstractMigration
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
        $table = $this->table('directus_fields');

        $table->addColumn('collection', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('field', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('type', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('interface', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('options', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('locked', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false
        ]);
        $table->addColumn('translation', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('required', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => false
        ]);
        $table->addColumn('sort', 'integer', [
            'signed' => false,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('comment', 'string', [
            'limit' => 1024,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('hidden_input', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => 0
        ]);
        $table->addColumn('hidden_list', 'boolean', [
            'signed' => false,
            'null' => false,
            'default' => 0
        ]);

        $table->addIndex(['collection', 'field'], [
            'unique' => true,
            'name' => 'idx_collection_field'
        ]);

        $table->create();
    }
}
