<?php


use Phinx\Migration\AbstractMigration;

class AddFileMaxSizeSetting extends AbstractMigration
{
    public function up()
    {
        $fieldObject = [
            'field' => 'file_max_size',
            'type' => 'string',
            'interface' => 'text-input',
            'options'   => json_encode(['placeholder' => '10k or 10M']),
            'note' => 'It accepts value like 10k,10M etc.',
        ];
        $collection = 'directus_settings';

        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSqlFormat = "INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`,`options`,`note`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s');";
            $insertSql = sprintf($insertSqlFormat, $collection, $fieldObject['field'], $fieldObject['type'], $fieldObject['interface'], $fieldObject['options'], $fieldObject['note']);
            $this->execute($insertSql);
        }

    }
}
