<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../api/api.php';
require __DIR__ . '/Thumbnailer.php';

use Directus\Util\ArrayUtils;

try {
    if (! file_exists(__DIR__ . '/../api/config.php') || filesize(__DIR__ . '/../api/config.php') == 0) {
        throw new Exception('Invalid Directus config.');
    }
    
    $app = \Directus\Bootstrap::get('app');

    $thumbnailer = new Thumbnailer([
        'thumbnailUrlPath' => $app->request->getPathInfo(),
        'configFilePath' => __DIR__ . '/config.json', 
    ]);
    
    // now we can create the thumb
    switch ($thumbnailer->action) {
        
        // http://image.intervention.io/api/crop
        case 'contain':
            $filePath = $thumbnailer->contain();
            break;
            
        // http://image.intervention.io/api/fit
        case 'crop':
        default:
            $filePath = $thumbnailer->crop();
    }
    
    header('HTTP/1.1 200 OK');
    header('Content-type: ' . image_type_to_mime_type(exif_imagetype($filePath)));
    echo file_get_contents($filePath);
    exit(0);
}

// all exceptions are handled by displaying an 'image not found' png
catch (Exception $e) {
    $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);    
    header('Content-type: ' . image_type_to_mime_type(exif_imagetype(ArrayUtils::get($config, '404imageLocation', './img-not-found.png'))));
    echo file_get_contents($filePath);
    exit(0);
}