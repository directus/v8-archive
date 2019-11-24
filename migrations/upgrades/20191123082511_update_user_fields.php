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

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'required' => 0
            ],
            ['collection' => 'directus_users', 'field' => 'locale']
        ));

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
        
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'sort' => 15
            ],
            ['collection' => 'directus_users', 'field' => 'locale_options']
        ));
        
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'sort' => 16
            ],
            ['collection' => 'directus_users', 'field' => 'token']
        ));
        
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'sort' => 17
            ],
            ['collection' => 'directus_users', 'field' => 'last_access_on']
        ));
        
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'sort' => 18
            ],
            ['collection' => 'directus_users', 'field' => 'last_page']
        ));



        // Delete from fields table
       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" and `field` = "last_login";')->fetch();
       if ($result) {
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "last_login";');
       }

       $usersTable = $this->table('directus_users');
       if ($usersTable->hasColumn('last_login')) {
           $usersTable->removeColumn('last_login');
       }

       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" and `field` = "invite_token";')->fetch();
       if ($result) {
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "invite_token";');
       }

       if ($usersTable->hasColumn('invite_token')) {
           $usersTable->removeColumn('invite_token');
       }

       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" and `field` = "invite_accepted";')->fetch();
       if ($result) {
           $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_users" and  `field` = "invite_accepted";');
       }

       if ($usersTable->hasColumn('invite_accepted')) {
           $usersTable->removeColumn('invite_accepted');
       }

       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" and `field` = "last_ip";')->fetch();
       if ($result) {
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
        
       $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_users" AND `field` = "theme"')->fetch();

        if (!$result) {
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
}
