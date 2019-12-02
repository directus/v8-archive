<?php


use Phinx\Migration\AbstractMigration;
use function \Directus\get_random_string;

class UpdateDirectusFiles extends AbstractMigration
{
    public function change()
    {
        $fieldsTable = $this->table('directus_fields');
        $filesTable = $this->table('directus_files');

        if($this->checkFieldExist('directus_files', 'filename')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'field' => 'filename_disk'
                ],
                ['collection' => 'directus_files', 'field' => 'filename']
            ));
        }

        if (!$filesTable->hasColumn('filename_disk')) {
            $filesTable->renameColumn('filename', 'filename_disk');
        }

        if (!$filesTable->hasColumn('filename_download')) {
            $filesTable->addColumn('filename_download', 'string', [
                'limit' => 255,
                'null' => false
            ])->save();
        }
        if(!$this->checkFieldExist('directus_files', 'filename_download')){
            $fieldsTable->insert([
                'collection' => 'directus_files',
                'field' => 'filename_download',
                'type' => 'string',
                'interface'=>'text-input',
                'locked' => 1
            ])->save();
        }

        // adds private_hash in directus_files collection for existing data
        if ($filesTable->hasColumn('private_hash')) {
            $result= $this->fetchAll('SELECT * FROM directus_files WHERE private_hash is null;');
            if(count($result) > 0) {
                foreach($result as $key=>$value) {
                    $this->execute(\Directus\phinx_update(
                        $this->getAdapter(),
                        'directus_files',
                        [
                            'private_hash' => get_random_string()
                        ],
                        ['id' => $value['id']]
                    ));
                }
            }
        }

        // Make duration read only
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            ['readonly' => 1],
            ['collection' => 'directus_files', 'field' => 'duration']
        ));

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
                        'uuid' => 'UUID (Obfuscated)',
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
    public function checkSettingExist($field){
        $checkSql = sprintf('SELECT 1 FROM `directus_settings` WHERE `key` = "%s"', $field);
        return $this->query($checkSql)->fetch();
    }

}
