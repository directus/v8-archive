<?php


use Phinx\Migration\AbstractMigration;

class UpdateUserRoleType extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Update user role type from M2M to O2M and delete the junction table directus_user_roles
     */
    public function change()
    {
        $result = $this->query('SELECT 1 FROM `directus_roles` WHERE `name` = "public";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_roles',
                [
                    'description' => 'Controls what API data is publicly available without authenticating'
                ],
                ['name' => 'public']
            ));
        }
        
        if($this->checkFieldExist('directus_roles','users')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'one-to-many'
                ],
                ['collection' => 'directus_roles', 'field' => 'users']
            ));
        }
        
        if($this->checkFieldExist('directus_users','roles')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'field' => 'role',
                    'type' => 'm2o'
                ],
                ['collection' => 'directus_users', 'field' => 'roles']
            ));
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_user_roles";')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_user_roles";');
        }

        $usersTable = $this->table('directus_users');
        if (!$usersTable->hasColumn('role')) {
            $usersTable->addColumn('role', 'integer', [
                'null' => true,
                'default' => null
            ])->save();
        }

        $result = $this->query('SELECT 1 FROM `directus_relations` WHERE `collection_many` = "directus_user_roles";')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_relations` where `collection_many` = "directus_user_roles";');
        }

        $fileds = $this->table('directus_relations');
        $fileds->insert([
            'collection_many' => 'directus_users',
            'field_many' => 'role',
            'collection_one' => 'directus_roles',
            'field_one' => 'users',
        ])->save();
       
        $userRolesTable = $this->table('directus_user_roles');
        if($userRolesTable->exists()){
            $stmt = $this->query("SELECT * FROM `directus_user_roles`");
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $this->execute('UPDATE `directus_users` SET `role` = '.$row['role'].' where id = '.$row['user'].';');
            }
            $userRolesTable->drop();
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
