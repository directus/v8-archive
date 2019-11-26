<?php


use Phinx\Migration\AbstractMigration;

class UpdateRepeaterTranslations extends AbstractMigration
{
    /**
     * Version : v8.0.1
     */
    public function change()
    {
        if($this->checkFieldExist('directus_collections','translation')){
            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'repeater',
                    'hidden_detail' => 0,
                    'options' => '{
                        "fields": [
                            {
                                "field": "locale",
                                "type": "string",
                                "interface": "language",
                                "options": {
                                    "limit": true
                                },
                                "width": "half"
                            },
                            {
                                "field": "translation",
                                "type": "string",
                                "interface": "text-input",
                                "width": "half"
                            }
                        ]
                    }'
                ],
                ['collection' => 'directus_collections', 'field' => 'translation']
            )); 
        }

        if($this->checkFieldExist('directus_fields','translation')){

            $this->execute(\Directus\phinx_update(
                $this->getAdapter(),
                'directus_fields',
                [
                    'interface' => 'repeater',
                    'hidden_detail' => 0,
                    'options' => '{
                        "fields": [
                            {
                                "field": "locale",
                                "type": "string",
                                "interface": "language",
                                "options": {
                                    "limit": true
                                },
                                "width": "half"
                            },
                            {
                                "field": "translation",
                                "type": "string",
                                "interface": "text-input",
                                "width": "half"
                            }
                        ]
                    }'
                ],
                ['collection' => 'directus_fields', 'field' => 'translation']
            ));
        }
    }

    public function checkFieldExist($collection,$field){
        $checkSql = sprintf('SELECT 1 FROM `directus_fields` WHERE `collection` = "%s" AND `field` = "%s";', $collection, $field);
        return $this->query($checkSql)->fetch();
    }
}
