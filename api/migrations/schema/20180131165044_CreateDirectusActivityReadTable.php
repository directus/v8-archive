<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusActivityReadTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_activity_read', [
            'id' => false
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('activity', 'integer', ['limit' => 11, 'null' => false, 'unsigned' => true]);
        $t->column('user', 'integer', ['unsigned' => true, 'null' => false, 'default' => 0]);
        // $t->column('datetime', 'datetime', ['default' => NULL]); WHEN WAS READ IT
        $t->column('read', 'tinyinteger', ['limit' => 1, 'default' => 0]);
        $t->column('archived', 'tinyinteger', ['limit' => 1, 'default' => 0]);
        $t->finish();

    }//up()

    public function down()
    {
        $this->drop_table('directus_activity');
    }//down()
}
