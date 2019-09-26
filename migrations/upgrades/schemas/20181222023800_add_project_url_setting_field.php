<?php

use Phinx\Migration\AbstractMigration;

class AddProjectUrlSettingField extends AbstractMigration
{
    public function up()
    {
        $keyColumn = $this->adapter->quoteColumnName('key');
        $result = $this->query("SELECT 1 FROM directus_settings WHERE $keyColumn = 'project_url';")->fetch();

        if (!$result) {
            $this->execute("INSERT INTO directus_settings ($keyColumn, value) VALUES ('project_url', '');");
        }
    }
}
