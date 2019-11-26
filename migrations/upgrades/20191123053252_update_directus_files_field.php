<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusFilesField extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Update the fields of directus_files table
     */
    public function change()
    {
        if($this->checkFieldExist('directus_files', 'uploaded_by')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'type' => \Directus\Database\Schema\DataTypes::TYPE_USER_CREATED,
                'interface' => 'user-created'
                ],
                ['collection' => 'directus_files', 'field' => 'uploaded_by']
            ));
        }

        if($this->checkFieldExist('directus_files', 'tags')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'placeholder' => 'Enter a keyword then hit enter...'
                    ]),
                ],
                ['collection' => 'directus_files', 'field' => 'tags']
            ));
        }

        if($this->checkFieldExist('directus_files', 'description')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'toolbar' => ['bold','italic','underline','link','code']
                    ]),
                ],
                ['collection' => 'directus_files', 'field' => 'description']
            ));
        }

        if($this->checkFieldExist('directus_files', 'metadata')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'width' => 'full'
                ],
                ['collection' => 'directus_files', 'field' => 'metadata']
            ));
        }  
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
