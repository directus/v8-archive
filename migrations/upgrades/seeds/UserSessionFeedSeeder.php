<?php


use Phinx\Seed\AbstractSeed;

class UserSessionFeedSeeder extends AbstractSeed
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
            // User Session
            // -----------------------------------------------------------------
            [
                'collection' => 'directus_user_sessions',
                'field' => 'id',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_INTEGER,
                'interface' => 'primary-key',
                'locked' => 1,
                'required' => 1,
                'hidden_detail' => 1
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'user',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_USER,
                'interface' => 'user'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'token_type',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'token',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'ip_address',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'user_agent',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_STRING,
                'interface' => 'text-input'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'created_on',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
            [
                'collection' => 'directus_user_sessions',
                'field' => 'token_expired_at',
                'type' => \Directus\Database\Schema\DataTypes::TYPE_DATETIME,
                'interface' => 'datetime'
            ],
        ];

        $files = $this->table('directus_fields');
        $files->insert($data)->save();
    }
}
