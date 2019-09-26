<?php


use Phinx\Migration\AbstractMigration;

class AddTrustedProxiesSettingField extends AbstractMigration
{
    public function up()
    {
        $keyColumn = $this->adapter->quoteColumnName('key');
        $result = $this->query("SELECT 1 FROM directus_settings WHERE $keyColumn = 'trusted_proxies';")->fetch();

        if (!$result) {
            $this->execute("INSERT INTO directus_settings ($keyColumn, value) VALUES ('trusted_proxies', '');");
        }
    }
}
