<?php


use Phinx\Migration\AbstractMigration;

class RemoveUtilities extends AbstractMigration
{
    /**
     * Remove schemas and seeds folder from upgrades and db folder. Rename db folder to install.
     */
    public function change()
    {

        $basePath = \Directus\Application\Application::getInstance()->getContainer()->get('path_base');
        rmdir($basePath.'/migrations/upgrades/schemas');
        rmdir($basePath.'/migrations/upgrades/seeds');
        rmdir($basePath.'/migrations/db/schemas');
        rmdir($basePath.'/migrations/db/seeds');
        rename($basePath.'/migrations/db','/migrations/install');
    }
}
