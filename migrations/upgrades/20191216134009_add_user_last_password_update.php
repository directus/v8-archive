<?php


use Phinx\Migration\AbstractMigration;

class AddUserLastPasswordUpdate extends AbstractMigration
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
        $usersTable = $this->table('directus_users');
        $fieldsTable = $this->table('directus_fields');

        // -------------------------------------------------------------------------
        // Add role column to directus_users
        // -------------------------------------------------------------------------
        if ($usersTable->hasColumn('password_last_updated_at') == false) {
            $usersTable->addColumn('password_last_updated_at', 'datetime', [
                'null' => true,
                'default' => null
            ]);
            $usersTable->save();
        }
        if (!$this->checkFieldExist('directus_users', 'password_last_updated_at')) {
            $fieldsTable->insert([
                'collection' => 'directus_users',
                'field' => 'password_last_updated_at',
                'type' =>  \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime',
                'locked' => 1,
                'readonly' => 1,
                'hidden_detail' => 1
            ])->save();
        }
    }

    public function checkFieldExist($collection, $field)
    {
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
