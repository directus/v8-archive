<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusFields extends AbstractMigration
{
    /**
     * Update directus fields table
     */
    public function change()
    {
        $fieldsTable = $this->table('directus_fields');

        if($fieldsTable->hasColumn('width')){
            $fieldsTable->changeColumn('width', 'string', [
                'limit' => 30,
                'null' => true,
                'default' => null,
            ]);
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `interface` = "single-file";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                ['interface' => 'file'],
                ['interface' => 'single-file']
            ));
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `width` = "1";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                ['width' => 'half'],
                ['width' => '1']
            ));
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `width` = "2";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                ['width' => 'half'],
                ['width' => '2']
            ));
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `width` = "3";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                ['width' => 'half'],
                ['width' => '3']
            ));
        }

        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `width` = "4";')->fetch();
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                ['width' => 'half'],
                ['width' => '4']
            ));
        }

        if($this->checkFieldExist('directus_folders', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_folders', 'interface' => 'code']
            ));
        }
        
        if($this->checkFieldExist('directus_migrations', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_migrations', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_permissions', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_permissions', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_relations', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_relations', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_revisions', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_revisions', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_roles', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_roles', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_settings', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_users', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'interface' => 'json'
                ],
                ['collection' => 'directus_users', 'interface' => 'code']
            ));
        }

    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
