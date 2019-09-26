<?php


use Phinx\Migration\AbstractMigration;

class AddAppUrlSettingField extends AbstractMigration
{
    public function up()
    {
        $this->addSetting();
        $this->addField();
    }

    protected function addSetting()
    {
        $key = 'app_url';
        $keyColumn = $this->adapter->quoteColumnName('key');
        $checkSql = sprintf("SELECT 1 FROM directus_settings WHERE $keyColumn = '%s';", $key);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSql = sprintf("INSERT INTO directus_settings ($keyColumn, value) VALUES ('%s', '');", $key);
            $this->execute($insertSql);
        }
    }

    protected function addField()
    {
        $collection = 'directus_settings';
        $field = 'app_url';
        $checkSql = sprintf("SELECT 1 FROM directus_fields WHERE collection = '%s' AND field = '%s';", $collection, $field);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $insertSqlFormat = "INSERT INTO directus_fields (collection, field, type, interface) VALUES ('%s', '%s', '%s', '%s');";
            $insertSql = sprintf($insertSqlFormat, $collection, $field, 'string', 'text-input');
            $this->execute($insertSql);
        }
    }
}
