<?php


use Phinx\Migration\AbstractMigration;

class UpdateUserFields extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Update/insert/delete users table field from directus_fields and direct_users table
     */
    public function change()
    {
        if($this->checkFieldExist('directus_users','locale')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'required' => 0
                ],
                ['collection' => 'directus_users', 'field' => 'locale']
            ));
        }

        if($this->checkFieldExist('directus_users','first_name')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'account_circle'           
                    ]),
                ],
                ['collection' => 'directus_users', 'field' => 'first_name']
            ));
        }

        if($this->checkFieldExist('directus_users','last_name')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'account_circle'           
                    ]),
                ],
                ['collection' => 'directus_users', 'field' => 'last_name']
            ));
        }

        if($this->checkFieldExist('directus_users','email')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'alternate_email'           
                    ]),
                ],
                ['collection' => 'directus_users', 'field' => 'email']
            ));
        }
        
        if($this->checkFieldExist('directus_users','company')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'location_city'           
                    ]),
                ],
                ['collection' => 'directus_users', 'field' => 'company']
            ));
        }
        
        if($this->checkFieldExist('directus_users','title')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'text_fields'           
                    ]),
                ],
                ['collection' => 'directus_users', 'field' => 'title']
            ));
        }
        
        if($this->checkFieldExist('directus_users','locale_options')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'sort' => 15
                ],
                ['collection' => 'directus_users', 'field' => 'locale_options']
            ));
        }
        
        if($this->checkFieldExist('directus_users','token')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'sort' => 16
                ],
                ['collection' => 'directus_users', 'field' => 'token']
            ));
        }
        
        if($this->checkFieldExist('directus_users','last_access_on')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'sort' => 17
                ],
                ['collection' => 'directus_users', 'field' => 'last_access_on']
            ));
        }
        
        if($this->checkFieldExist('directus_users','last_page')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'sort' => 18
                ],
                ['collection' => 'directus_users', 'field' => 'last_page']
            ));
        }


        // Delete from fields table
       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" and `field` = "last_login";')->fetch();
       if ($result) {
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "last_login";');
       }

       $usersTable = $this->table('directus_users');
       if ($usersTable->hasColumn('last_login')) {
           $usersTable->removeColumn('last_login');
       }

       if($this->checkFieldExist('directus_users','invite_token')){
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "invite_token";');
       }

       if ($usersTable->hasColumn('invite_token')) {
           $usersTable->removeColumn('invite_token');
       }

       if($this->checkFieldExist('directus_users','invite_accepted')){
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "invite_accepted";');
       }

       if ($usersTable->hasColumn('invite_accepted')) {
           $usersTable->removeColumn('invite_accepted');
       }

       if($this->checkFieldExist('directus_users','last_ip')){
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "last_ip";');
       }

       if ($usersTable->hasColumn('last_ip')) {
           $usersTable->removeColumn('last_ip');
       }

       //Add field
       if (!$usersTable->hasColumn('theme')) {
           $usersTable->addColumn('theme', 'string', [
               'limit' => 255,
               'null' => true,
               'default' => null
           ])->save();
        }
        
        if (!$this->checkFieldExist('directus_users','theme')) {
            $options =  json_encode([
                'format' => true,
                'choices' => [
                    'auto' => 'Auto',
                    'light' => 'Light',
                    'dark' => 'Dark'
                ]
            ]);
            $this->execute("INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`, `options`, `locked`, `readonly`, `sort`) VALUES ('directus_users', 'theme', 'string', 'radio-buttons', '".$options."', 1,0 , 14);");
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
