<?php

namespace Directus\Application\Http;

class Response extends \Slim\Http\Response
{
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
