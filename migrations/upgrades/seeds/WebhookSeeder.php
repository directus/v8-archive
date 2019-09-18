<?php


use Phinx\Seed\AbstractSeed;

class WebhookSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'collection' => 'directus_webhooks',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'primary-key',
                'locked' => 1,
                'required' => 1,
                'hidden_detail' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'collection',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'required' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'directus_action',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'required' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'url',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'required' => 1
            ],
            [
                'collection' => 'directus_webhooks',
                'field' => 'http_action',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input',
                'required' => 1
            ]
            
        ];
        $files = $this->table('directus_fields');
        $files->insert($data)->save();
    }
}
