<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusCollectionPresetsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_collection_presets', [
            'id' => false
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('title', 'string', [
            'limit' => 128,
            'null' => true,
            'default' => null
        ]);
        $t->column('user', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => false
        ]);
        $t->column('group', 'integer', [
            'limit' => 11,
            'unsigned' => true,
            'null' => true
        ]);
        $t->column('collection', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('fields', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('statuses', 'string', [
            'limit' => 64,
            'null' => true,
            'default' => null
        ]);
        $t->column('sort', 'string', [
            'limit' => 255,
            'null' => true,
            'default' => null
        ]);
        $t->column('search_string', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('filters', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('view_options', 'text', [
            'null' => true,
            'default' => null
        ]);

        $t->finish();

        $this->add_index('directus_collection_presets', ['user', 'collection', 'title'], [
            'unique' => true,
            'name' => 'user_collection_title'
        ]);
    }//up()

    public function down()
    {
        $this->remove_index('directus_preferences', ['user', 'table_name', 'title'], [
            'unique' => true,
            'name' => 'user_table_title'
        ]);
        $this->drop_table('directus_preferences');
    }//down()
}
