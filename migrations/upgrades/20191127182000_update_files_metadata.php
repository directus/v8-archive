<?php


use Phinx\Migration\AbstractMigration;

class UpdateFilesMetadata extends AbstractMigration
{
    /**
     * Version: v8.0.0
     *
     * Change:
     * Replace the JSON interface for metadata with a key value pair interface
     */
    public function change() {
        if ($this->checkFieldExist('directus_files', 'metadata')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                    'interface' => 'key-value',
                    'locked' => 1,
                    'sort' => 14,
                    'width' => 'full',
                    'options' => json_encode([
                        'keyInterface' => 'text-input',
                        'keyDataType' => 'string',
                        'keyOptions' => [
                            'monospace' => true,
                            'placeholder' => 'Key'
                        ],
                        'valueInterface' => 'text-input',
                        'valueDataType' => 'string',
                        'valueOptions' => [
                            'monospace' => true,
                            'placeholder' => 'Value'
                        ]
                    ])
                ],
                ['collection' => 'directus_files', 'field' => 'metadata']
            ));
        }
    }

    public function checkFieldExist($collection, $field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
