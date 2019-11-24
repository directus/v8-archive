<?php


use Phinx\Migration\AbstractMigration;

class RemoveUtilities extends AbstractMigration
{
    /**
     * Version : v8.0.1
     * Remove schemas and seeds folder from upgrades and db folder. 
     */
    public function change()
    {

        $basePath = \Directus\Application\Application::getInstance()->getContainer()->get('path_base');
        $this->delete_files($basePath.'/migrations/upgrades/schemas');
        $this->delete_files($basePath.'/migrations/upgrades/seeds');
        $this->delete_files($basePath.'/migrations/db');
    }

    public function delete_files($path) {
        if(is_dir($path) == TRUE){
            $rootFolder = scandir($path);
            if(sizeof($rootFolder) > 2){
                foreach($rootFolder as $folder){
                    if($folder != "." && $folder != ".."){
                        //Pass the subfolder to function
                        $this->delete_files($path."/".$folder);
                    }
                }
                //On the end of foreach the directory will be cleaned, and you will can use rmdir, to remove it
                rmdir($path);
            }
        }else{
            if(file_exists($path) == TRUE){
                unlink($path);
            }
        }
    }
}
