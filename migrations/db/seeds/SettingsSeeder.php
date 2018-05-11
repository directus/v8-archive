<?php

use Phinx\Seed\AbstractSeed;

class SettingsSeeder extends AbstractSeed
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
                'scope' => 'global',
                'key' => 'auto_sign_out',
                'value' => '60'
            ],
            [
                'scope' => 'global',
                'key' => 'file_naming',
                'value' => 'file_id'
            ]
        ];

        $files = $this->table('directus_settings');
        $files->insert($data)->save();
    }
}
