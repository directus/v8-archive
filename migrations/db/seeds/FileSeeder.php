<?php


use Phinx\Seed\AbstractSeed;

class FileSeeder extends AbstractSeed
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
            'id' => 1,
            'filename' => '00000000001.jpg',
            'title' => 'Mountain Range',
            'description' => 'A gorgeous view of this wooded mountain range',
            'location' => 'Earth',
            'tags' => 'trees,rocks,nature,mountains,forest',
            'width' => 1800,
            'height' => 1200,
            'filesize' => 602058,
            'type' => 'image/jpeg',
            'charset' => 'binary',
            'upload_user' => 1,
            'upload_date' => \Directus\Util\DateUtils::now(),
            'storage_adapter' => 'local'
        ];

        $files = $this->table('directus_files');
        $files->insert($data)->save();
    }
}
