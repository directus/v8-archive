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
            $insertSqlFormat = 'INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`) VALUES ("%s", "%s", "%s", "%s");';
            $insertSql = sprintf($insertSqlFormat, $collection, $field, 'string', 'text-input');
            $this->execute($insertSql);
        }
    }
}
