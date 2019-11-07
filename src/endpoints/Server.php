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
}
