<?php


use Phinx\Migration\AbstractMigration;

class AddThumbnailWhitelistSystemToSettingsTable extends AbstractMigration
{
    public function change()
    {
        $fieldObject = [
            'field' => 'thumbnail_whitelist_system',
            'type' => 'json',
            'interface' => 'json',
        ];
        $collection = 'directus_settings';

        $thumbnail_whitelist_system = [
            'key'   => 'thumbnail_whitelist_system',
            'value' =>  json_encode(
                        [
                            [
                                "key" => "card",
                                "width" => 200,
                                "height" => 200,
                                "fit" => "crop",
                                "quality" => 80
                            ],
                            [
                                "key" => "avatar",
                                "width" => 100,
                                "height" => 100,
                                "fit" => "crop",
                                "quality" => 80
                            ]
                        ])
        ];

        $groups = $this->table('directus_settings');
        $groups->insert($thumbnail_whitelist_system )->save();

        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $fieldObject['field']);
        $result = $this->query($checkSql)->fetch();

        if (!$result) {
            $data = [
                'collection' =>  $collection,
                'field' => $fieldObject['field'],
                'type' => $fieldObject['type'],
                'interface'=>$fieldObject['interface'],
                'readonly' => 1,
                'width' => 'half'
            ];

            $groups = $this->table('directus_fields');
            $groups->insert($data)->save();
        }
    }
}
