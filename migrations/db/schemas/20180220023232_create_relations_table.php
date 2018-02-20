<?php


use Phinx\Migration\AbstractMigration;

class CreateRelationsTable extends AbstractMigration
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
        $table = $this->table('directus_relations');

        $table->addColumn('collection_a', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('field_a', 'string', [
            'limit' => 45,
            'null' => false
        ]);
        $table->addColumn('junction_key_a', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $table->addColumn('junction_collection', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $table->addColumn('junction_mixed_collections', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $table->addColumn('junction_key_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $table->addColumn('collection_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $table->addColumn('field_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);

        $table->create();
    }
}
