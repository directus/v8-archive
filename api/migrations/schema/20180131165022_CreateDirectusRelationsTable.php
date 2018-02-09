<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusRelationsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_relations', [
            'id' => false
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('collection_a', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('field_a', 'string', [
            'limit' => 45,
            'null' => false
        ]);
        $t->column('junction_key_a', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->column('junction_collection', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->column('junction_mixed_collections', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->column('junction_key_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->column('collection_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->column('field_b', 'string', [
            'limit' => 64,
            'null' => true
        ]);
        $t->finish();

    }//up()

    public function down()
    {
        $this->drop_table('directus_relations');
    }//down()
}
