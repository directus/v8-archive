<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusPermissionsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_permissions', [
            'id' => false,
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('collection', 'string', [
            'limit' => 64,
            'null' => false,
        ]);
        $t->column('group', 'integer', [
            'unsigned' => true,
            'null' => false
        ]);
        $t->column('status', 'integer', [
            'limit' => 11,
            'default' => null,
            'null' => true
        ]);
        $t->column('create', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0,
        ]);
        $t->column('read', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0,
        ]);
        $t->column('update', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0,
        ]);
        $t->column('delete', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 0,
        ]);
        $t->column('navigate', 'tinyinteger', [
            'limit' => 1,
            'null' => false,
            'default' => 1
        ]);
        $t->column('read_field_blacklist', 'string', [
            'limit' => 1000,
            'null' => true,
            'default' => null,
            'character' => 'utf8'
        ]);
        $t->column('write_field_blacklist', 'string', [
            'limit' => 1000,
            'null' => true,
            'default' => NULL,
            'character' => 'utf8',
        ]);

        $t->finish();
    }//up()

    public function down()
    {
        $this->drop_table('directus_permissions');
    }//down()
}
