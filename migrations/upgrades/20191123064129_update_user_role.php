<?php


use Phinx\Migration\AbstractMigration;

class UpdateUserRole extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Update user role type from M2M to O2M and delete the junction table directus_user_roles
     */
    public function change()
    {
        $rolesTable =  $this->table('directus_roles');
        $fieldsTable =  $this->table('directus_fields');
        $usersTable = $this->table('directus_users');
        $userRolesTable = $this->table('directus_user_roles');
        $relationsTable = $this->table('directus_relations');

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
        
        if(!$this->checkFieldExist('directus_roles','users')){
            $fieldsTable->insert([
                'collection' => 'directus_roles',
                'field' => 'users',
                'type' => 'o2m',
                'interface' => 'many-to-many',
            ])->save();
        }

        if($this->checkFieldExist('directus_roles', 'users')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'one-to-many',
                    'options' => json_encode([
                        'fields' => 'first_name,last_name'
                    ])
                ],
                ['collection' => 'directus_roles', 'field' => 'users']
            ));
        }
        if($this->checkFieldExist('directus_roles','name')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'required' => 1
                ],
                ['collection' => 'directus_roles', 'field' => 'name']
            ));
        }

        if (!$rolesTable->hasColumn('nav_override')) {
            $rolesTable->addColumn('nav_override', 'text', [
                'null' => true
            ])->save();
        }
        
        if(!$this->checkFieldExist('directus_roles','nav_override')){
            $fieldsTable->insert([
                'collection' => 'directus_roles',
                'field' => 'nav_override',
                'type' => 'json',
                'interface' => 'code',
                'options' => json_encode([
                    'template' => [
                        0 => [
                            'title' => '$t:collections',
                            'include' => 'collections',
                        ],
                        1 => [
                            'title' => '$t:bookmarks',
                            'include' => 'bookmarks',
                        ],
                        2 => [
                            'title' => '$t:extensions',
                            'include' => 'extensions',
                        ],
                        3 => [
                            'title' => 'Custom Links',
                            'links' => [
                                0 => [
                                    'name' => 'RANGER Studio',
                                    'path' => 'https://rangerstudio.com',
                                    'icon' => 'star',
                                ],
                                1 => [
                                    'name' => 'Movies',
                                    'path' => '/collections/movies',
                                ]
                            ],
                        ],
                    ]
                ]),
                'locked' => 1,
            ])->save();
        }
        
        if (!$rolesTable->hasColumn('enforce_2fa')) {
            $rolesTable->addColumn('enforce_2fa', 'boolean', [
                'null' => true,
                'default' => null
            ])->save();
        }

        if(!$this->checkFieldExist('directus_users','2fa_secret')){
            $fieldsTable->insert([
                'collection' => 'directus_roles',
                'field' => 'enforce_2fa',
                'type' => 'boolean',
                'interface' => 'toggle'
            ])->save();
        }

        // Update M2M type for user role to O2M
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
        
        $result = $this->query('SELECT 1 FROM `directus_relations` WHERE `collection_many` = "directus_users" and `field_many` = "role"  and `collection_one` = "directus_roles" and  `field_one` = "users" ;')->fetch();

        if(!$result){
            $relationsTable->insert([
                'collection_many' => 'directus_users',
                'field_many' => 'role',
                'collection_one' => 'directus_roles',
                'field_one' => 'users',
            ])->save();
        }
       
        // Remove directus_user_roles table
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
