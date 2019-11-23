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

        $settings =get_directus_thumbnail_settings();
        $timeToLive = array_get($settings, 'thumbnail_cache_ttl', 86400);

        if(isset($response['image']) && $response['mimeType'])
        {
            header('HTTP/1.1 200 OK');
            header('Content-type: ' . $response['mimeType']);
            header("Pragma: cache");
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type");
            if(count($request->getQueryParams()) > 0)
            {
                header('Cache-Control: max-age=' . $timeToLive);
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T', time()));
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $timeToLive));
            }
            echo $response['image'];
        }
        else
        {
            return http_response_code(404);
        }
        exit(0);
    }
}