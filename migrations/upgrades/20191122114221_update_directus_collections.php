<?php


use Phinx\Migration\AbstractMigration;

class UpdateDirectusCollections extends AbstractMigration
{
    /**
     * This will update the directus_collections tale
     */
    public function change()
    {

        $collectionsTable = $this->table('directus_collections');
        
        if($collectionsTable->hasColumn('icon')){
            $collectionsTable->changeColumn('icon', 'string', [
                'limit' => 30,
                'null' => true,
                'default' => null,
            ]);
        }

        if($this->checkFieldExist('directus_collections', 'note')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                  'note' => 'An internal description.'
                ],
                ['collection' => 'directus_collections', 'field' => 'note']
            ));
        }
        if($this->checkFieldExist('directus_collection_presets', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'interface' => 'json'
                ],
                ['collection' => 'directus_collection_presets', 'interface' => 'code']
            ));
        }
        
        if($this->checkFieldExist('directus_collections', 'code')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                'interface' => 'json'
                ],
                ['collection' => 'directus_collections', 'interface' => 'code']
            ));
        }

        if($this->checkFieldExist('directus_collections', 'managed')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => '[Learn More](https://docs.directus.io/guides/collections.html#managing-collections).'
                ],
                ['collection' => 'directus_collections', 'field' => 'managed']
            ));
        }

        if($this->checkFieldExist('directus_collections', 'hidden')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => '[Learn More](https://docs.directus.io/guides/collections.html#hidden).'
                ],
                ['collection' => 'directus_collections', 'field' => 'hidden']
            ));
        }

        if($this->checkFieldExist('directus_collections', 'single')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => '[Learn More](https://docs.directus.io/guides/collections.html#single).'
                ],
                ['collection' => 'directus_collections', 'field' => 'single']
            ));
        }

        if($this->checkFieldExist('directus_collections', 'icon')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'note' => 'The icon shown in the App\'s navigation sidebar.'
                ],
                ['collection' => 'directus_collections', 'field' => 'icon']
            ));
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
