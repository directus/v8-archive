<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusRevisionsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        // TODO: Rename table to directus_activities?
        $t = $this->create_table('directus_revisions', [
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
        $t->column('collection', 'string', ['limit' => 64, 'null' => false]);
        $t->column('item', 'string', ['limit' => 255]);
        $t->column('data', 'text'); // TODO: long text?
        $t->column('delta', 'text'); // TODO: long text?
        $t->column('parent_item', 'string', ['limit' => 255]);
        $t->column('parent_collection', 'string', ['limit' => 64, 'null' => false]);
        $t->column('parent_changed', 'tinyinteger', ['limit' => 1, 'default' => 0]);
        $t->finish();

    }//up()

    public function down()
    {
        $this->drop_table('directus_activity');
    }//down()
}
