<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusSettingsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_settings', [
            'id' => false,
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('scope', 'string', [
            'limit' => 64,
            'default' => null
        ]);
        $t->column('group', 'string', [
            'limit' => 64,
            'default' => null
        ]);
        $t->column('key', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $t->column('value', 'string', [
            'limit' => 255,
            'default' => null
        ]);

        $t->finish();

        $this->add_index('directus_settings', ['scope', 'group', 'key'], [
            'unique' => true,
            'name' => 'idx_scope_group_key'
        ]);
    }//up()

    public function down()
    {
        $this->remove_index('directus_settings', ['scope', 'group', 'key'], [
            'unique' => true,
            'name' => 'idx_scope_group_key'
        ]);
        $this->drop_table('directus_settings');
    }//down()
}
