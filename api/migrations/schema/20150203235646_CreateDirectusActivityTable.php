<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusActivityTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_activity', [
            'id' => false,
            //'options'=> 'COMMENT="Contains history of revisions"'
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('type', 'string', ['limit' => 45, 'null' => false]);
        $t->column('action', 'string', ['limit' => 45, 'null' => false]);
        $t->column('user', 'integer', ['unsigned' => true, 'null' => false, 'default' => 0]);
        $t->column('datetime', 'datetime', ['default' => NULL]);
        $t->column('ip', 'string', ['limit' => 50, 'default' => NULL]);
        $t->column('user_agent', 'string', ['limit' => 255]);
        $t->column('collection', 'string', ['limit' => 64, 'null' => false]);
        $t->column('item', 'string', ['limit' => 255]);
        $t->column('message', 'text');
        $t->finish();

    }//up()

    public function down()
    {
        $this->drop_table('directus_activity');
    }//down()
}
