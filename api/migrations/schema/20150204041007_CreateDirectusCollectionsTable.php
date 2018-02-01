<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusCollectionsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_collections', [
            'id' => false,
        ]);

        // columns
        $t->column('collection', 'string', [
            'limit' => 64,
            'null' => false,
            'default' => '',
            'primary_key' => true
        ]);
        $t->column('item_name_template', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('preview_url', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('hidden', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0
        ]);
        $t->column('single', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0
        ]);
        $t->column('status_mapping', 'text', [
            'null' => true,
            'default' => null
        ]);

        $t->finish();
    }//up()

    public function down()
    {
        $this->drop_table('directus_collections');
    }//down()
}
