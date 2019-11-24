<?php


use Phinx\Migration\AbstractMigration;

class UpdateRepeaterTranslations extends AbstractMigration
{
    /**
     * Version : v8.0.1
     */
    public function change()
    {
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
