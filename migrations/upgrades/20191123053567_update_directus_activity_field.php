<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusActivityField extends AbstractMigration
{
    /**
     * Update the fields of directus_activity table
     */
    public function change()
    {

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
