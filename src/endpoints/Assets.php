<?php

namespace Directus\Api\Routes;

use Directus\Application\Route;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Services\AssetService;

class Assets extends Route
{
    public function __invoke(Request $request, Response $response)
    {
        $service = new AssetService($this->container);
        $fileId=$request->getAttribute('id');
    
        $asset=$service->getAsset(
            $fileId,
            $request->getQueryParams()
        );
        
        if(isset($asset['file']) && $asset['mimeType'])
        {
            $response->setHeader('Content-type',$asset['mimeType']);
            $response->setHeader('Content-Disposition','filename='.$asset['fileNameDownlaod']);
            $response->setHeader('Last-Modified',gmdate('D, d M Y H:i:s \G\M\T', time()));    
               
            $body = $response->getBody();
            $body->write($asset['file']);
            
            return $response;
        }
        else
        {
            return $response->withStatus(404);
        }
    }
}