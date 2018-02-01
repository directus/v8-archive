<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusGroupsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_groups', [
            'id' => false,
        ]);

        // columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('name', 'string', [
            'limit' => 100,
            'null' => false
        ]);
        $t->column('description', 'string', [
            'limit' => 500,
            'null' => true,
            'default' => NULL
        ]);
        $t->column('ip_whitelist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('nav_blacklist', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('nav_override', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->finish();

        $this->add_index('directus_groups', 'name', [
            'unique' => true,
            'name' => 'directus_users_name_unique'
        ]);

        $this->insert('directus_groups', [
            'id' => 1,
            'name' => 'Administrator',
            'description' => 'Admins have access to all managed data within the system by default'
        ]);

        $this->insert('directus_groups', [
            'id' => 2,
            'name' => 'Public',
            'description' => 'This sets the data that is publicly available through the API without a token'
        ]);
    }//up()

    public function down()
    {
        $this->drop_table('directus_groups');
    }//down()
}
