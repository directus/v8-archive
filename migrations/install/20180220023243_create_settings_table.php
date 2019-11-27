<?php

use Phinx\Migration\AbstractMigration;

class CreateSettingsTable extends AbstractMigration
{
    /**
     * Create Settings Table
     */
    public function change()
    {
        $table = $this->table('directus_settings', ['signed' => false]);

        $table->addColumn('key', 'string', [
            'limit' => 64,
            'null' => false
        ]);
        $table->addColumn('value', 'text', [
            'default' => null
        ]);

        $table->addIndex(['key'], [
            'unique' => true,
            'name' => 'idx_key'
        ]);

        $table->create();

        $data = [
            [
                'collection' => 'directus_settings',
                'field' => 'project_name',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'options' => json_encode([
                    'iconRight' => 'title'
                ]),
                'locked' => 1,
                'required' => 1,
                'width' => 'half',
                'note' => 'Logo in the top-left of the App (40x40)',
                'sort' => 1
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_url',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'options' => json_encode([
                    'iconRight' => 'link'
                ]),
                'locked' => 1,
                'width' => 'half',
                'note' => 'External link for the App\'s top-left logo',
                'sort' => 2
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_logo',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'file',
                'locked' => 1,
                'width' => 'half',
                'note' => 'A 40x40 brand logo, ideally a white SVG/PNG',
                'sort' => 3
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_color',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'color',
                'locked' => 1,
                'width' => 'half',
                'note' => 'Color for login background and App\'s logo',
                'sort' => 4
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_foreground',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'file',
                'locked' => 1,
                'width' => 'half',
                'note' => 'Centered image (eg: logo) for the login page',
                'sort' => 5
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'project_background',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_FILE,
                'interface' => 'file',
                'locked' => 1,
                'width' => 'half',
                'note' => 'Full-screen background for the login page',
                'sort' => 6
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'default_locale',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'language',
                'locked' => 1,
                'width' => 'half',
                'note' => 'Default locale for Directus Users',
                'sort' => 7,
                'options' => json_encode([
                    'limit' => true
                ])
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'telemetry',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle',
                'locked' => 1,
                'width' => 'half',
                'note' => '<a href="https://docs.directus.io/getting-started/concepts.html#telemetry" target="_blank">Learn More</a>',
                'sort' => 8
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'data_divider',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ALIAS,
                'interface' => 'divider',
                'options' => json_encode([
                    'style' => 'large',
                    'title' => 'Data',
                    'hr' => true
                ]),
                'locked' => 1,
                'width' => 'full',
                'hidden_browse' => 1,
                'sort' => 10
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'default_limit',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'numeric',
                'options' => json_encode([
                    'iconRight' => 'keyboard_tab'
                ]),
                'locked' => 1,
                'required' => 1,
                'width' => 'half',
                'note' => 'Default item count in API and App responses',
                'sort' => 11
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'sort_null_last',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_BOOLEAN,
                'interface' => 'toggle',
                'locked' => 1,
                'note' => 'NULL values are sorted last',
                'width' => 'half',
                'sort' => 12
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'security_divider',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ALIAS,
                'interface' => 'divider',
                'options' => json_encode([
                    'style' => 'large',
                    'title' => 'Security',
                    'hr' => true
                ]),
                'locked' => 1,
                'hidden_browse' => 1,
                'width' => 'full',
                'sort' => 20
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'auto_sign_out',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'numeric',
                'options' => json_encode([
                    'iconRight' => 'timer'
                ]),
                'locked' => 1,
                'required' => 1,
                'width' => 'half',
                'note' => 'Minutes before idle users are signed out',
                'sort' => 22
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'login_attempts_allowed',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'numeric',
                'options' => json_encode([
                    'iconRight' => 'lock'
                ]),
                'locked' => 1,
                'width' => 'half',
                'note' => 'Failed login attempts before suspending users',
                'sort' => 23
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'files_divider',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ALIAS,
                'interface' => 'divider',
                'options' => json_encode([
                    'style' => 'large',
                    'title' => 'Files & Thumbnails',
                    'hr' => true
                ]),
                'locked' => 1,
                'hidden_browse' => 1,
                'width' => 'full',
                'sort' => 30
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'file_max_size',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'options' => json_encode([
                    'placeholder' => 'eg: 4MB',
                    'iconRight' => 'storage'
                ]),
                'locked' => 1,
                'width' => 'half',
                'sort' => 31
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'file_mimetype_whitelist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_ARRAY,
                'interface' => 'tags',
                'options' => json_encode([
                    'placeholder' => 'Enter a file mimetype then hit enter (eg: image/jpeg)'
                ]),
                'locked' => 1,
                'width' => 'full',
                'sort' => 32
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'thumbnail_whitelist',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json',
                'width' => 'half',
                'note' => 'Defines how the thumbnail will be generated based on the requested params.',
                'sort' => 33
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'thumbnail_whitelist_system',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_JSON,
                'interface' => 'json',
                'readonly' => 1,
                'width' => 'half',
                'sort' => 34
            ],
            [
                'collection' => 'directus_settings',
                'field' => 'youtube_api_key',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'options' => json_encode([
                    'iconRight' => 'videocam'
                ]),
                'locked' => 1,
                'width' => 'half',
                'note' => 'Allows fetching more YouTube Embed info',
                'sort' => 35
            ],
        ];

        foreach($data as $value){
            if(!$this->checkFieldExist($value['collection'], $value['field'])){
                $fileds = $this->table('directus_fields');
                $fileds->insert($value)->save();
            }
        }

        // Insert into settings table
        $data = [
            [
              'key' => 'project_url',
              'value' => ''
            ],
            [
              'key' => 'project_logo',
              'value' => ''
            ],
            [
              'key' => 'project_color',
              'value' => 'blue-grey-900',
            ],
            [
              'key' => 'project_foreground',
              'value' => '',
            ],
            [
              'key' => 'project_background',
              'value' => '',
            ],
            [
              'key' => 'default_locale',
              'value' => 'en-US',
            ],
            [
              'key' => 'telemetry',
              'value' => '1',
            ],
            [
              'key' => 'default_limit',
              'value' => '200'
            ],
            [
              'key' => 'sort_null_last',
              'value' => '1'
            ],
            [
              'key' => 'password_policy',
              'value' => ''
            ],
            [
              'key' => 'auto_sign_out',
              'value' => '10080'
            ],
            [
              'key' => 'login_attempts_allowed',
              'value' => '10'
            ],
            [
              'key' => 'trusted_proxies',
              'value' => ''
            ],
            [
              'key' => 'file_max_size',
              'value' => '100MB'
            ],
            [
              'key' => 'file_mimetype_whitelist',
              'value' => ''
            ],
            [
              'key' => 'youtube_api_key',
              'value' => ''
            ],
            [
                'key' => 'thumbnail_whitelist_system',
                'value' => json_encode([
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
            ]
          ];

          $groups = $this->table('directus_settings');
          $groups->insert($data)->save();
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
