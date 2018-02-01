<?php

use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class CreateDirectusUsersTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('directus_users', [
            'id' => false,
            'charset' => 'utf8mb4'
        ]);

        //columns
        $t->column('id', 'integer', [
            'unsigned' => true,
            'null' => false,
            'auto_increment' => true,
            'primary_key' => true
        ]);
        $t->column('status', 'tinyinteger', [
            'unsigned' => true,
            'limit' => 1,
            'default' => 2 // Inactive
        ]);
        $t->column('first_name', 'string', [
            'limit' => 50,
            'default' => ''
        ]);
        $t->column('last_name', 'string', [
            'limit' => 50,
            'default' => ''
        ]);
        $t->column('email', 'string', [
            'limit' => 128,
            'null' => false,
            'default' => ''
        ]);
        $t->column('email_notifications', 'tinyinteger', [
            'limit' => 1,
            'default' => 1
        ]);
        $t->column('group', 'integer', [
            'unsigned' => true,
            'null' => true,
            'default' => null
        ]);
        $t->column('password', 'string', [
            'limit' => 255,
            'character' => 'utf8',
            'null' => true,
            'default' => null
        ]);
        $t->column('avatar', 'integer', [
            'unsigned' => true,
            'limit' => 11,
            'null' => true,
            'default' => null
        ]);
        $t->column('company', 'string', [
            'limit' => 191,
            'null' => true,
            'default' => null
        ]);
        $t->column('title', 'string', [
            'limit' => 191,
            'null' => true,
            'default' => null
        ]);
        $t->column('locale', 'string', [
            'limit' => 8,
            'null' => true,
            'default' => 'en-US'
        ]);
        $t->column('locale_options', 'text', [
            'null' => true,
            'default' => null
        ]);
        $t->column('timezone', 'string', [
            'limit' => 32,
            'default' => 'America/New_York'
        ]);
        $t->column('last_ip', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null
        ]);
        $t->column('last_login', 'datetime', [
            'null' => true,
            'default' => null
        ]);
        $t->column('last_access', 'datetime', [
            'null' => true,
            'default' => null
        ]);
        $t->column('last_page', 'string', [
            'limit' => 45,
            'null' => true,
            'default' => null
        ]);
        $t->column('token', 'string', [
            'limit' => 255,
            'character' => 'utf8',
            'null' => true,
            'default' => null
        ]);
        $t->column('invite_token', 'string', [
            'limit' => 255,
            'default' => null
        ]);
        $t->column('invite_accepted', 'tinyinteger', [
            'limit' => 1,
            'default' => null
        ]);

        $t->finish();

        $this->add_index('directus_users', 'email', [
            'unique' => true,
            'name' => 'directus_users_email_unique'
        ]);

        $this->add_index('directus_users', 'token', [
            'unique' => true,
            'name' => 'directus_users_token_unique'
        ]);
    }//up()

    public function down()
    {
        $this->drop_table('directus_users');
    }//down()
}
