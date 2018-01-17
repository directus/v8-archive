<?php

/**
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