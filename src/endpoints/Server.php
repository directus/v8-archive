<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Route;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Exception\NotInstalledException;
use Directus\Util\StringUtils;

class Server extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        \Directus\create_ping_route($app);
        $app->get('/projects', [$this, 'projects']);
        $app->get('/check-requirements', [$this, 'checkRequirements']);
    }

    /**
     * Return the projects
     * 
     * @return Response
     */
    public function projects(Request $request, Response $response)
    {
        $scannedDirectory = \Directus\scan_config_folder();
        $projectNames = [];
        if(empty($scannedDirectory)){
            throw new NotInstalledException('This Directus instance has not been configured. Install via the Directus App (eg: /admin) or read more about configuration at: https://docs.directus.io/getting-started/installation.html#configure');
        }else{
            foreach($scannedDirectory as $fileName){
                $fileObject = explode(".",$fileName);
                if(!StringUtils::startsWith($fileObject[0], '_')){
                    $projectNames[] = $fileObject[0];
                }
            }
        }
  
        $responseData['data'] = $projectNames;
        return $this->responseWithData($request, $response, $responseData);
    }	    

     /**
     * Return the current setup of server.
     * 
     * @return Response
     */
    public function checkRequirements(Request $request, Response $response)
    {
        $basePath = $this->container->get('path_base');
        $responseData['data'] = [
            'os' => PHP_OS,
            'os_version' => php_uname('v'),
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'php_version' => phpversion(),
            'extensions' => [
                'pdo_enabled' => defined('PDO::ATTR_DRIVER_NAME'),
                'mysqli_enabled' => extension_loaded("mysqli"),
                'curl_enabled' => extension_loaded("curl"),
                'gd_enabled' => extension_loaded("gd"),
                'fileinfo_enabled' => extension_loaded("fileinfo"),
                'libapache2_mod_php_enabled' => extension_loaded("libapache2-mod-php"),
                'mbstring_enabled' => extension_loaded("mbstring"),
                'json_enabled' => extension_loaded("json"),
                'mod_rewrite_enabled' =>function_exists('apache_get_modules') ? in_array('mod_rewrite', apache_get_modules()) : null,
            ],
            'file_permission' => [
                'public' => substr(sprintf('%o', fileperms($basePath."/public")), -4),
                'logs' => substr(sprintf('%o', fileperms($basePath."/logs")), -4),
                'uploads' => substr(sprintf('%o', fileperms($basePath."/public/uploads")), -4),
            ]
        ];
        return $this->responseWithData($request, $response, $responseData);
    }	    
}
