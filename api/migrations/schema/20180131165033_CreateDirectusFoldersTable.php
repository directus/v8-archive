<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusFoldersTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_folders', [
            'id' => false
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('name', 'string', [
            'limit' => 191,
            'null' => false
        ]);
        $t->column('parent_folder', 'integer', [
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->finish();

        $this->add_index('directus_folders', ['name', 'parent_folder'], [
            'unique' => true,
            'name' => 'idx_name_parent_folder'
        ]);

    }//up()

    public function down()
    {
        $this->remove_index('directus_folders', ['name', 'parent_folder'], [
            'unique' => true,
            'name' => 'idx_name_parent_folder'
        ]);

        $this->drop_table('directus_folders');
    }//down()
}
