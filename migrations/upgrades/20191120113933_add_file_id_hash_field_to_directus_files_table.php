<?php


use Phinx\Migration\AbstractMigration;

class AddFileIdHashFieldToDirectusFilesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('directus_files');
        if (!$table->hasColumn('hash_id')) {
            $table->addColumn('hash_id', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null
            ]);
            $table->save();
        }

        $collection = 'directus_files';
        $field = 'hash_id';
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $data = [
                        'collection' =>  $collection,
                        'field' => $field,
                        'type' => 'string',
                        'interface'=>'text-input',
                        'readonly' => 1,
                        'hidden_browse' => 1,
                        'hidden_detail' => 1
                    ];
    
            $groups = $this->table('directus_fields');
            $groups->insert($data)->save();
        }
    }
}
