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

/**
 * @param \Psr\Http\Message\ResponseInterface $response
 *
 * @return array|object
 */
function response_get_data(\Psr\Http\Message\ResponseInterface $response)
{
    $object = response_to_object($response);

    return $object->data;
}
