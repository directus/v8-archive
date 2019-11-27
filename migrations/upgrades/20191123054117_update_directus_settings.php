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
        $table = $this->table('directus_settings');
        $fieldsTable = $this->table('directus_fields');

        if ($table->hasColumn('scope')) {
            if ($table->hasIndex('idx_scope_name')) {
                $table->removeIndex('idx_scope_name');
            }
            $table->removeColumn('scope')
                ->save();
            $table->addIndex(['key'], [
                'unique' => true,
                'name' => 'idx_key'
            ]);
        }
        
        if ($table->hasColumn('value')) {
            $table->changeColumn('value', 'text', [
                'null' => true
            ]);
        }

        if (!$this->checkSettingExist('trusted_proxies')) {
            $table->insert([
                'key' => 'trusted_proxies',
                'value' => null
            ])->save();
        }

        if (!$this->checkSettingExist('project_url')) {
            $table->insert([
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
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'project_name')){     
            $fieldsTable->insert([
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
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'default_limit')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'default_limit',
                'type' => 'integer',
                'interface' => 'numeric',
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'sort_null_last')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'sort_null_last',
                'type' => 'boolean',
                'interface' => 'toggle',
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'auto_sign_out')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'auto_sign_out',
                'type' => 'integer',
                'interface' => 'numeric',
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'youtube_api_key')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'youtube_api_key',
                'type' => 'string',
                'interface' => 'text-input',
            ])->save();
        }
            
        // Updating Settings Table
        if($this->checkFieldExist('directus_settings', 'data_divider')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'hidden_browse' => 1,
                ],
                ['collection' => 'directus_settings', 'field' => 'data_divider']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'security_divider')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'hidden_browse' => 1,
                ],
                ['collection' => 'directus_settings', 'field' => 'security_divider']
            )); 
        }

        if($this->checkFieldExist('directus_settings', 'files_divider')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'hidden_browse' => 1,
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
                ['collection' => 'directus_settings', 'field' => 'files_divider']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'project_name')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'sort' => 1,
                    'options' => json_encode([
                        'iconRight' => 'title'
                    ]),
                    'width' => 'half',
                    'note' => 'Logo in the top-left of the App (40x40)',
                ],
                ['collection' => 'directus_settings', 'field' => 'project_name']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'project_logo')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'locked' => 1,
                    'width' => 'half',
                    'note' => 'A 40x40 brand logo, ideally a white SVG/PNG',
                    'sort' => 3
                ],
                ['collection' => 'directus_settings', 'field' => 'project_logo']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'default_limit')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'keyboard_tab'
                    ]),
                    'locked' => 1,
                    'required' => 1,
                    'width' => 'half',
                    'note' => 'Default item count in API and App responses',
                    'sort' => 11
                ],
                ['collection' => 'directus_settings', 'field' => 'default_limit']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'sort_null_last')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'locked' => 1,
                    'note' => 'NULL values are sorted last',
                    'width' => 'half',
                    'sort' => 12
                ],
                ['collection' => 'directus_settings', 'field' => 'sort_null_last']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'auto_sign_out')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'timer'
                    ]),
                    'locked' => 1,
                    'required' => 1,
                    'width' => 'half',
                    'note' => 'Minutes before idle users are signed out',
                    'sort' => 22
                ],
                ['collection' => 'directus_settings', 'field' => 'auto_sign_out']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'thumbnail_dimensions')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'placeholder' => 'Allowed dimensions for thumbnails (eg: 200x200)'
                    ]),
                    'locked' => 1,
                    'width' => 'full',
                    'note' => 'Allowed dimensions for thumbnails.',
                    'sort' => 34
                ],
                ['collection' => 'directus_settings', 'field' => 'thumbnail_dimensions']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'thumbnail_quality_tags')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => 'Allowed qualities for thumbnails',
                    'sort' => 35
                ],
                ['collection' => 'directus_settings', 'field' => 'thumbnail_quality_tags']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'thumbnail_actions')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => 'Defines how the thumbnail will be generated based on the requested dimensions',
                    'sort' => 36
                ],
                ['collection' => 'directus_settings', 'field' => 'thumbnail_actions']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'thumbnail_not_found_location')){     
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
        }
        
        
        if($this->checkFieldExist('directus_settings', 'thumbnail_cache_ttl')){     
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
        
        if($this->checkFieldExist('directus_settings', 'youtube_api_key')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'options' => json_encode([
                        'iconRight' => 'videocam'
                    ]),
                    'locked' => 1,
                    'width' => 'half',
                    'note' => 'Allows fetching more YouTube Embed info',
                    'sort' => 39
                ],
                ['collection' => 'directus_settings', 'field' => 'youtube_api_key']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'file_naming')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'locked' => 1,
                    'width' => 'half',
                    'note' => 'File-system naming convention for uploads',
                    'sort' => 31,
                    'options' => json_encode([
                        'choices' => [
                            'uuid' => 'File Hash (Obfuscated)',
                            'file_name' => 'File Name (Readable)'
                        ]
                    ])
                ],
                ['collection' => 'directus_settings', 'field' => 'file_naming']
            ));
        }

        if(!$this->checkFieldExist('directus_settings', 'login_attempts_allowed')){     
            $fieldsTable->insert([
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
            ])->save();
        }
        if($this->checkFieldExist('directus_settings', 'login_attempts_allowed')){        
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'width' => 'half'
                ],
                ['collection' => 'directus_settings', 'field' => 'login_attempts_allowed']
            ));
        }

        if(!$this->checkFieldExist('directus_settings', 'file_mimetype_whitelist')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'file_mimetype_whitelist',
                'type' => 'array',
                'interface' => 'tags',
                'options' => json_encode([
                    'placeholder' => 'Enter a file mimetype then hit enter (eg: image/jpeg)'
                ]),
                'locked' => 1,
                'width' => 'full',
                'sort' => 33
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'file_max_size')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'file_max_size',
                'type' => 'string',
                'interface' => 'text-input',
                'options' => json_encode([
                    'placeholder' => 'eg: 4MB',
                    'iconRight' => 'storage'
                ]),
                'locked' => 1,
                'width' => 'half',
                'sort' => 32
            ])->save();
        }
        if($this->checkFieldExist('directus_settings', 'file_max_size')){   
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'width' => 'half',
                ],
                ['collection' => 'directus_settings', 'field' => 'file_max_size']
            ));
        }

        if(!$this->checkFieldExist('directus_settings', 'password_policy')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'password_policy',
                'type' => 'string',
                'note' => 'Weak : Minimum length 8; Strong :  1 small-case letter, 1 capital letter, 1 digit, 1 special character and the length should be minimum 8',
                'interface' => 'dropdown',
                'options'   => ['choices' => ['' => 'None', '/^.{8,}$/' => 'Weak', '/(?=^.{8,}$)(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+}{\';\'?>.<,])(?!.*\s).*$/' => 'Strong']]
            ])->save();
        }
        
        if($this->checkFieldExist('directus_settings', 'password_policy')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'width' => 'half',
                ],
                ['collection' => 'directus_settings', 'field' => 'password_policy']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'color')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'width' => 'half',
                    'note' => 'The color that best fits your brand.',
                    'sort' => 2,
                    'field' => 'project_color'
                ],
                ['collection' => 'directus_settings', 'field' => 'color']
            ));
        }

        if ($this->checkSettingExist('project_color')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_settings',
                [
                'key' => 'project_color'
                ],
                ['key' => 'color']
            ));
        }

        if($this->checkFieldExist('directus_settings', 'logo')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'width' => 'half',
                    'note' => 'Your brand\'s logo.',
                    'sort' => 3,
                    'field' => 'project_logo',
                    'interface' => 'file',
                ],
                ['collection' => 'directus_settings', 'field' => 'logo']
            ));
        }

        if ($this->checkSettingExist('logo')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_settings',
                [
                'key' => 'project_logo'
                ],
                ['key' => 'logo']
            ));
        }

        if ($this->checkSettingExist('auto_sign_out')) {
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_settings',
                [
                    'value' => '10080'
                ],
                ['key' => 'auto_sign_out']
            ));
        }

        // Delete from Settings table
        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `field` = "app_url";')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_fields` where `field` = "app_url";');
        }

        if ($this->checkSettingExist('app_url')) {
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

    public function checkSettingExist($field){
        $checkSql = sprintf('SELECT 1 FROM `directus_settings` WHERE `key` = "%s"', $field);
        return $this->query($checkSql)->fetch();
    }

}
