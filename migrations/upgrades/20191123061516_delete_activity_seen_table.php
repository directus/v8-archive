<?php


use Phinx\Migration\AbstractMigration;

class DeleteActivitySeenTable extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Delete directus_activity_seen table
     */
    public function change()
    {
        $result = $this->query('SELECT 1 FROM `directus_fields` WHERE `collection` = "directus_activity_seen"')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_fields` WHERE `collection` = "directus_activity_seen"');
        }

        $result = $this->query('SELECT 1 FROM `directus_relations` WHERE `collection_many` = "directus_activity_seen"')->fetch();
        if ($result) {
            $this->execute('DELETE FROM `directus_relations` WHERE `collection_many` = "directus_activity_seen"');
        }
        $activitySeenTable = $this->table('directus_activity_seen');
        if ($activitySeenTable->exists()) {
            $activitySeenTable->drop();
        }
    }
}
