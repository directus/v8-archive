<?php

/**
 * Gets the response (JSON) parsed contents
 *
 * @param \Psr\Http\Message\ResponseInterface $response
 *
 * @return object
 */
function response_to_object(\Psr\Http\Message\ResponseInterface $response)
{
    // rewind the pointer to the beginning
    // after getting the content, you must rewind or the content is a empty
    $response->getBody()->rewind();

    return json_decode($response->getBody()->getContents());
}

/**
 * Gets the response object data
 *
 * @param \Psr\Http\Message\ResponseInterface $response
 *
 * @return array|object
 */
function response_get_data(\Psr\Http\Message\ResponseInterface $response)
{
    $object = response_to_object($response);

    return $object->data;
}

/**
 * Gets the request body contents
 *
 * @param \Psr\Http\Message\ResponseInterface $response
 *
 * @return string
 */
function response_get_body_contents(\Psr\Http\Message\ResponseInterface $response)
{
    $response->getBody()->rewind();

    return $response->getBody()->getContents();
}

function clear_storage($basePath)
{
    $uploadsOmit = ['.gitignore', '.htaccess', '00000000001.jpg'];
    foreach (glob($basePath . "/*.*") as $filename) {
        $name = basename($filename);
        if (is_dir($filename) || in_array($name, $uploadsOmit)) {
            continue;
        }

        unlink($filename);
    }

    $thumbsOmit = ['.gitignore', '1.jpg'];
    $thumbsPath = $basePath . '/thumbs';
    foreach (glob($thumbsPath . "/*.*") as $filename) {
        $name = basename($filename);
        if (is_dir($filename) || in_array($name, $thumbsOmit)) {
            continue;
        }

        unlink($filename);
    }
}
