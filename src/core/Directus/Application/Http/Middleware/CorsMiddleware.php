<?php

namespace Directus\Application\Http\Middleware;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Util\ArrayUtils;
use Directus\Util\StringUtils;

class CorsMiddleware extends AbstractMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $corsOptions = $this->getOptions();
        if (ArrayUtils::get($corsOptions, 'enabled', false)) {
            $this->processHeaders($request, $response);
        }

        if (!$request->isOptions()) {
            return $next($request, $response);
        }

        return $response;
    }

    /**
     * Sets the headers
     *
     * @param Request $request
     * @param Response $response
     */
    protected function processHeaders(Request $request, Response $response)
    {
        $corsOptions = $this->getOptions();
        $origin = $this->getOrigin($request);

        if ($origin) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            foreach (ArrayUtils::get($corsOptions, 'headers', []) as $name => $value) {
                // Support two options:
                // 1. [Key, Value]
                // 2. Key => Value
                if (is_array($value)) {
                    // using $value will make name the first value character of $value value
                    $temp = $value;
                    list($name, $value) = $temp;
                }

                $response->setHeader($name, $value);
            }
        }
    }

    /**
     * Gets the header origin
     *
     * This is the origin the header is going to be used
     *
     * There are four different scenario's for possibly returning an
     * Access-Control-Allow-Origin header:
     *
     * 1) null - don't return header
     * 2) '*' - return header '*'
     * 3) {str} - return header {str}
     * 4) [{str}, {str}, {str}] - if origin matches, return header {str}
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getOrigin(Request $request)
    {
        $corsOptions = $this->getOptions();
        $requestOrigin = $request->getOrigin();
        $allowedOrigins = ArrayUtils::get($corsOptions, 'origin', '*');

        return \Directus\cors_get_allowed_origin($allowedOrigins, $requestOrigin);
    }

    /**
     * Gets CORS options
     *
     * @return array
     */
    protected function getOptions()
    {
        $config = $this->container->get('config');

        return $config->get('cors', []);
    }
}
