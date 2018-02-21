<?php

namespace Directus\Application\Http;

class Response extends \Slim\Http\Response
{
    const HTTP_NOT_FOUND            = 404;
    const HTTP_METHOD_NOT_ALLOWED   = 405;

    /**
     * Sets multiple headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        foreach($headers as $name => $value) {
            $this->headers->set($name, $value);
        }

        return $this;
    }

    /**
     * Sets key-value header information
     *
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setHeader($name, $value)
    {
        return $this->withHeaders([$name => $value]);
    }
}
