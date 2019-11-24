<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusSettings extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * This will update the column name of directus_settings table as well as update it in directus_fields table
     */
    public function change()
    {
        // Updating Settings Table
        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'hidden_browse' => 1,
            ],
            ['collection' => 'directus_settings', 'field' => 'data_divider']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'hidden_browse' => 1,
            ],
            ['collection' => 'directus_settings', 'field' => 'security_divider']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'hidden_browse' => 1,
            ],
            ['collection' => 'directus_settings', 'field' => 'files_divider']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'options' => json_encode([
                    'iconRight' => 'title'
                ]),
                'width' => 'half',
                'note' => 'Logo in the top-left of the App (40x40)',
            ],
            ['collection' => 'directus_settings', 'field' => 'project_name']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'note' => 'A 40x40 brand logo, ideally a white SVG/PNG',
            ],
            ['collection' => 'directus_settings', 'field' => 'project_logo']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'options' => json_encode([
                    'iconRight' => 'keyboard_tab'
                ]),
                'note' => 'Default item count in API and App responses',
                'sort' => 11
            ],
            ['collection' => 'directus_settings', 'field' => 'default_limit']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'note' => 'NULL values are sorted last',
            ],
            ['collection' => 'directus_settings', 'field' => 'sort_null_last']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'options' => json_encode([
                    'iconRight' => 'timer'
                ]),
                'note' => 'Minutes before idle users are signed out',
                'sort' => 22
            ],
            ['collection' => 'directus_settings', 'field' => 'auto_sign_out']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'options' => json_encode([
                    'placeholder' => 'Allowed dimensions for thumbnails (eg: 200x200)'
                ]),
                'width' => 'full',
                'sort' => 34
            ],
            ['collection' => 'directus_settings', 'field' => 'thumbnail_dimensions']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'note' => 'Allowed qualities for thumbnails',
                'sort' => 35
            ],
            ['collection' => 'directus_settings', 'field' => 'thumbnail_quality_tags']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'note' => 'Defines how the thumbnail will be generated based on the requested dimensions',
                'sort' => 36
            ],
            ['collection' => 'directus_settings', 'field' => 'thumbnail_actions']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
                'options' => json_encode([
                    'iconRight' => 'broken_image'                    
                ]),
                'locked' => 1,
                'width' => 'full',
                'note' => 'A fallback image used when thumbnail generation fails',
                'sort' => 37
            ],
            ['collection' => 'directus_settings', 'field' => 'thumbnail_not_found_location']
        ));
        
        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_settings" and `field` = "thumbnail_cache_ttl";')->fetch();
        
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'cached'
                    ]),
                    'required' => 1,
                    'note' => 'Seconds before browsers re-fetch thumbnails',
                    'sort' => 38
                ],
                ['collection' => 'directus_settings', 'field' => 'thumbnail_cache_ttl']
            ));
        }
        
        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_settings" and `field` = "youtube_api";')->fetch();
        
        if ($result) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'videocam'
                    ]),
                    'width' => 'half',
                    'note' => 'Allows fetching more YouTube Embed info',
                    'sort' => 39
                ],
                ['collection' => 'directus_settings', 'field' => 'youtube_api']
            ));
        }

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
              'width' => 'half'
            ],
            ['collection' => 'directus_settings', 'field' => 'file_naming']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
              'width' => 'half'
            ],
            ['collection' => 'directus_settings', 'field' => 'login_attempts_allowed']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
            'field' => 'project_color'
            ],
            ['collection' => 'directus_settings', 'field' => 'color']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_settings',
            [
            'key' => 'project_color'
            ],
            ['key' => 'color']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_fields',
            [
              'field' => 'project_logo'
            ],
            ['collection' => 'directus_settings', 'field' => 'logo']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_settings',
            [
              'key' => 'project_logo'
            ],
            ['key' => 'logo']
        ));

        $this->execute(\Directus\phinx_update(
            $this->getAdapter(),
            'directus_settings',
            [
                'value' => '10080'
            ],
            ['key' => 'auto_sign_out']
        ));

        // Delete from Settings table
        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `field` = "app_url";')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_fields` where `field` = "app_url";');
        }

        $result = $this->query('SELECT 1 FROM `directus_settings` WHERE `key` = "app_url";')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "app_url";');
        }


        // Insert new settings
        $data = [
            [
                'collection' => 'directus_settings',
                'field' => 'project_foreground',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'file',
                'width' => 'half',
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_background',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'file',
                'width' => 'half',
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'default_locale',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'language',
                'options' => json_encode([
                    'limit' => true
                ]),
                'width' => 'half',
                'note' => 'Default locale for Directus Users',
                'sort' => 7
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'telemetry',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle',
                'width' => 'half',
                'note' => '<a href="https://docs.directus.io/getting-started/concepts.html#telemetry" target="_blank">Learn More</a>',
                'sort' => 8
            ]
        ];
        
        foreach($data as $value){
            if(!$this->checkFieldExist($value['collection'], $value['field'])){
                $fileds = $this->table('directus_fields');
                $fileds->insert($value)->save();
            }
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
