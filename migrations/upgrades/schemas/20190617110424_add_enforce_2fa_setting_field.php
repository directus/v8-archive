<?php


use Phinx\Migration\AbstractMigration;

class AddEnforce2FASettingField extends AbstractMigration
{
    public function up()
    {
        $this->addSetting();
        $this->addField();
    }

    protected function addSetting()
    {
        $key = 'enforce_2fa';
        $checkSql = sprintf('SELECT 1 FROM `directus_settings` WHERE `key` = "%s";', $key);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSql = sprintf('INSERT INTO `directus_settings` (`key`, `value`) VALUES ("%s", "");', $key);
            $this->execute($insertSql);
        }
    }

    protected function addField()
    {
        $collection = 'directus_settings';
        $field = 'enforce_2fa';
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSqlFormat = 'INSERT INTO `directus_fields` (`collection`, `field`, `type`, `interface`) VALUES ("%s", "%s", "%s", "%s");';
            $insertSql = sprintf($insertSqlFormat, $collection, $field, 'boolean', 'toggle');
            $this->execute($insertSql);
        }
    }
}
