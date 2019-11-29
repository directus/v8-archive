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
                'null' => true,
                'default' => null
            ])->save();
        }
        if(!$this->checkFieldExist('directus_files', 'filename_download')){
            $fieldsTable->insert([
                'collection' => 'directus_files',
                'field' => 'filename_download',
                'type' => 'string',
                'interface'=>'text-input',
                'locked' => 1,
                'readonly' => 1,
                'hidden_detail' => 1,
                'hidden_browse' => 1
            ])->save();
        }
        if($filesTable->hasColumn('id')){
            $filesTable->changeColumn('id', 'integer');
        }

        if($this->checkFieldExist('directus_files','id')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'type' => 'integer'
                ],
                ['collection' => 'directus_files', 'field' => 'id']
            ));
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
