<?php


use Phinx\Migration\AbstractMigration;

class AddFileNamingInDirectusSettings extends AbstractMigration
{
    public function change()
    {
        $fieldsTable = $this->table('directus_fields');
        $settingsTable = $this->table('directus_settings');
        
        if(!$this->checkFieldExist('directus_settings', 'file_naming')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'file_naming',
                'type' => 'string',
                'interface'=>'dropdown',
                'locked' => 1,
                'width' => 'half',
                'note' => 'File-system naming convention for uploads',
                'options' => json_encode([
                    'choices' => [
                        'uuid' => 'File Hash (Obfuscated)',
                        'file_name' => 'File Name (Readable)'
                    ]
                ])
            ])->save();
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
