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
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
            'type' => \Directus\Database\Schema\DataTypes::TYPE_USER_CREATED,
            'interface' => 'user-created'
            ],
            ['collection' => 'directus_files', 'field' => 'uploaded_by']
        ));
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
