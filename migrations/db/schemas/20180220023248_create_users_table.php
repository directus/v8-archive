<?php


use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('directus_users');

        $table->addColumn('status', 'integer', [
            'signed' => false,
            'limit' => 1,
            'default' => 2 // Inactive
        ]);
        $table->addColumn('first_name', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('last_name', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('email', 'string', [
            'limit' => 128,
            'null' => false
        ]);
        $table->addColumn('email_notifications', 'integer', [
            'limit' => 1,
            'default' => 1
        ]);
        $table->addColumn('group', 'integer', [
            'signed' => false,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('password', 'string', [
            'limit' => 255,
            'encoding' => 'utf8',
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('avatar', 'integer', [
            'signed' => false,
            'limit' => 11,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('company', 'string', [
            'limit' => 191,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('title', 'string', [
            'limit' => 191,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('locale', 'string', [
            'limit' => 8,
            'null' => true,
            'default' => 'en-US'
        ]);
        $table->addColumn('locale_options', 'text', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('timezone', 'string', [
            'limit' => 32,
            'default' => 'America/New_York'
        ]);
        $table->addColumn('last_ip', 'string', [
            'limit' => 50,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('last_login', 'datetime', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('last_access', 'datetime', [
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('last_page', 'string', [
            'limit' => 45,
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('token', 'string', [
            'limit' => 255,
            'encoding' => 'utf8',
            'null' => true,
            'default' => null
        ]);
        $table->addColumn('invite_token', 'string', [
            'limit' => 255,
            'default' => null
        ]);
        $table->addColumn('invite_accepted', 'boolean', [
            'signed' => false,
            'default' => false
        ]);

        $table->addIndex('email', [
            'unique' => true,
            'name' => 'idx_users_email'
        ]);

        $table->addIndex('token', [
            'unique' => true,
            'name' => 'idx_users_token'
        ]);

        $table->create();
    }
}
