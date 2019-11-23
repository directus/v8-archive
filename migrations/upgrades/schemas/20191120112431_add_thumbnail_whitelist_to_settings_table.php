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
            'note' => 'Defines how the thumbnail will be generated based on the requested params.',
        ];
        $collection = 'directus_settings';

        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $data = [
                'collection' =>  $collection,
                'field' => $fieldObject['field'],
                'type' => $fieldObject['type'],
                'interface'=>$fieldObject['interface'],
                'note' => $fieldObject['note'],
                'width' => 'half'
            ];

            $groups = $this->table('directus_fields');
            $groups->insert($data)->save();
        }
    }
}
