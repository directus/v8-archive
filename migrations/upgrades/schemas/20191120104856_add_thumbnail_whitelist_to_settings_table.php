<?php


use Phinx\Migration\AbstractMigration;

class AddThumbnailWhitelistToSettingsTable extends AbstractMigration
{
    public function change()
    {
        $fieldObject = [
            'field' => 'thumbnail_whitelist',
            'type' => 'json',
            'interface' => 'json',
            'note' => 'Defines how the thumbnail will be generated based on the requested details.',
        ];
        $collection = 'directus_settings';

        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSqlFormat = "INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`, `note`) VALUES ('%s', '%s', '%s', '%s', '%s');";
            $insertSql = sprintf($insertSqlFormat, $collection, $fieldObject['field'], $fieldObject['type'], $fieldObject['interface'],$fieldObject['note']);
            $this->execute($insertSql);
        }
    }
}
