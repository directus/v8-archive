<?php


use Phinx\Migration\AbstractMigration;

class UpdateThumbnailSettings extends AbstractMigration
{
    /**
     * Update thumbnail settings
     */
    public function change()
    {

        $settingsTable = $this->table('directus_settings');
        $fieldsTable = $this->table('directus_fields');
        $filesTable = $this->table('directus_files');

        if(!$this->checkFieldExist('directus_settings', 'thumbnail_whitelist')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'thumbnail_whitelist',
                'type' => 'json',
                'interface' => 'json',
                'width' => 'half',
                'note' => 'Defines how the thumbnail will be generated based on the requested params.',
            ])->save();
        }

        if (!$this->checkSettingExist('thumbnail_whitelist_system')) {
            $settingsTable->insert([
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
            ])->save();
        }

        if(!$this->checkFieldExist('directus_settings', 'thumbnail_whitelist_system')){     
            $fieldsTable->insert([
                'collection' => 'directus_settings',
                'field' => 'thumbnail_whitelist_system',
                'type' => 'json',
                'interface' => 'json',
                'readonly' => 1,
                'width' => 'half'
            ])->save();
        }

        if (!$filesTable->hasColumn('private_hash')) {
            $filesTable->addColumn('private_hash', 'string', [
                'limit' => 16,
                'null' => true,
                'default' => null
            ])->save();
        }

        if(!$this->checkFieldExist('directus_files', 'private_hash')){     
            $fieldsTable->insert([
                'collection' => 'directus_files',
                'field' => 'private_hash',
                'type' => 'string',
                'interface'=>'text-input',
                'readonly' => 1,
                'hidden_browse' => 1,
                'hidden_detail' => 1
            ])->save();
        }

        if($filesTable->hasColumn('id')){
            $filesTable->changeColumn('id', 'string', [
                'limit' => 16
            ]);
        }

        if($this->checkFieldExist('directus_files','id')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'type' => 'string'
                ],
                ['collection' => 'directus_files', 'field' => 'id']
            ));
        }

        if (!$this->checkSettingExist('thumbnail_dimensions')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "thumbnail_dimensions";');
        }
 
        if($this->checkFieldExist('directus_settings','thumbnail_dimensions')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "thumbnail_dimensions";');
        }

        if (!$this->checkSettingExist('thumbnail_quality_tags')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "thumbnail_quality_tags";');
        }
 
        if($this->checkFieldExist('directus_settings','thumbnail_quality_tags')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "thumbnail_quality_tags";');
        }

        if (!$this->checkSettingExist('thumbnail_actions')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "thumbnail_actions";');
        }
 
        if($this->checkFieldExist('directus_settings','thumbnail_actions')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "thumbnail_actions";');
        }

        if (!$this->checkSettingExist('thumbnail_cache_ttl')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "thumbnail_cache_ttl";');
        }
 
        if($this->checkFieldExist('directus_settings','thumbnail_cache_ttl')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "thumbnail_cache_ttl";');
        }

        if (!$this->checkSettingExist('thumbnail_not_found_location')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "thumbnail_not_found_location";');
        }
 
        if($this->checkFieldExist('directus_settings','thumbnail_not_found_location')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "thumbnail_not_found_location";');
        }

        if (!$this->checkSettingExist('file_naming')) {
            $this->execute('DELETE FROM `directus_settings` where `key` = "file_naming";');
        }
 
        if($this->checkFieldExist('directus_settings','file_naming')){
            $this->execute('DELETE FROM `directus_fields` where `collection` = "directus_settings" and  `field` = "file_naming";');
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
