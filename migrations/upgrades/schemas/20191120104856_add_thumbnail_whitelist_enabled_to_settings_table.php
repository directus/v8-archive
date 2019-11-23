<?php


use Phinx\Migration\AbstractMigration;

class AddThumbnailWhitelistEnabledToSettingsTable extends AbstractMigration
{
    public function change()
    {
        $fieldObject = [
            'field' => 'thumbnail_whitelist_enabled',
            'type' => 'boolean',
            'interface' => 'toggle'
        ];
        $collection = 'directus_settings';

        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();

        $thumbnail_whitelist_enabled = [
            'key'   => 'thumbnail_whitelist_enabled',
            'value' => 0,
        ];

        $groups = $this->table('directus_settings');
        $groups->insert($thumbnail_whitelist_enabled )->save();

        if (!$result) {

            $data = [
                'collection' =>  $collection,
                'field' => $fieldObject['field'],
                'type' => $fieldObject['type'],
                'interface'=>$fieldObject['interface']
            ];

            $groups = $this->table('directus_fields');
            $groups->insert($data)->save();
        }
    }
}
