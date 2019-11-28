<?php


use Phinx\Migration\AbstractMigration;

class UseTagsForIpWhitelist extends AbstractMigration
{
    /**
     * Version: v8.0.0
     *
     * Change:
     * Use the tags interface for IP whitelist on user roles instead of a textarea
     */
    public function change() {
        if ($this->checkFieldExist('directus_roles', 'ip_whitelist')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'type' => \Directus\Database\Schema\DataTypes::TYPE_ARRAY,
                    'interface' => 'tags',
                    'options' => json_encode([
                        '' => 'Add an IP address...'
                    ])
                ],
                ['collection' => 'directus_roles', 'field' => 'ip_whitelist']
            ));
        }
    }

    public function checkFieldExist($collection, $field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
