<?php

namespace Directus\Api\Routes;

use Directus\Application\Route;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Services\AssetService;
use function Directus\array_get;
use function Directus\get_directus_thumbnail_settings;

class Assets extends Route
{
    public function __invoke(Request $request, Response $response)
    {
        $service = new AssetService($this->container);
        $fileId=$request->getAttribute('id');
    
        $response=$service->getAsset(
            $fileId,
            $request->getQueryParams()
        );
        
        if(isset($response['file']) && $response['mimeType'])
        {
            header('HTTP/1.1 200 OK');
            header('Content-type: ' . $response['mimeType']);
            header("Pragma: cache");
            header("Content-Disposition: filename=".$response['filename']);
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type");
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
               
            echo $response['file'];
        }
        else
        {
            header('HTTP/1.1 404 Not Found');
        }
        exit(0);
    }
}