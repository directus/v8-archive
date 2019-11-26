<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusActivityField extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Update the fields of directus_activity table
     */
    public function change()
    {
        if($this->checkFieldExist('directus_activity', 'action')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'change_history'
                    ]),
                    'width' => 'full'
                ],
                ['collection' => 'directus_activity', 'field' => 'action']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'collection')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'list_alt'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'collection']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'item')){      
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'link'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'item']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'action_by')){      
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'account_circle'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'action_by']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'action_on')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'showRelative' => true,
                        'iconRight' => 'calendar_today'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'action_on']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'edited_on')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'showRelative' => true,
                        'iconRight' => 'edit'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'edited_on']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'comment_deleted_on')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'showRelative' => true,
                        'iconRight' => 'delete_outline'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'comment_deleted_on']
            ));
        }
        
        if($this->checkFieldExist('directus_activity', 'ip')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'my_location'
                    ])
                ],
                ['collection' => 'directus_activity', 'field' => 'ip']
            ));
        }

        if($this->checkFieldExist('directus_activity', 'user_agent')){     
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'options' => json_encode([
                        'iconRight' => 'devices_other'
                    ]),
                    'width' => 'full'
                ],
                ['collection' => 'directus_activity', 'field' => 'user_agent']
            ));
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
