<?php

namespace Directus\Application\Http\Middleware;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Util\ArrayUtils;
use Psr\Container\ContainerInterface;

class CorsMiddleware extends AbstractMiddleware
{
    /**
     * Force CORS headers processing
     *
     * @var bool
     */
    protected $force;

    protected $defaults = [
        'origin' => ['*'],
        'methods' => [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD'
        ],
        'headers' => [],
        'exposed_headers' => [],
        'max_age' => null,
        'credentials' => false,
    ];

    /**
     * @var null|array
     */
    protected $options = null;

    public function __construct(ContainerInterface $container, $force = false)
    {
        parent::__construct($container);
        $this->force = $force;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $options = $this->getOptions();
        $corsEnabled = $this->force === true || ArrayUtils::get($options, 'enabled', false);

        if ($corsEnabled) {
            if ($request->isOptions()) {
                $this->processPreflightHeaders($request, $response);
                return $response;
            } else {
                $this->processActualHeaders($request, $response);
            }
        }

        return $next($request, $response);
    }

    /**
     * Sets the preflight response headers
     *
     * @param Request $request
     * @param Response $response
     */
    protected function processPreflightHeaders(Request $request, Response $response)
    {
        $headers = [];

        array_push($headers, $this->createOriginHeader($request));
        array_push($headers, $this->createAllowedMethodsHeader());
        array_push($headers, $this->createAllowedHeadersHeader($request));
        array_push($headers, $this->createExposedHeadersHeader());
        array_push($headers, $this->createMaxAgeHeader());
        array_push($headers, $this->createCredentialsHeader());

        $this->setHeaders($response, $headers);
    }

    /**
     * Sets the actual response headers
     *
     * @param Request $request
     * @param Response $response
     */
    protected function processActualHeaders(Request $request, Response $response)
    {
        $headers = [];

        array_push($headers, $this->createOriginHeader($request));
        array_push($headers, $this->createExposedHeadersHeader());
        array_push($headers, $this->createCredentialsHeader());

        $this->setHeaders($response, $headers);
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
        $options = $this->getOptions();
        $requestOrigin = $request->getOrigin();
        $allowedOrigins = ArrayUtils::get($options, 'origin', '*');

        return \Directus\cors_get_allowed_origin($allowedOrigins, $requestOrigin);
    }

    /**
     * Gets CORS options
     *
     * @return array
     */
    protected function getOptions()
    {
        if ($this->options === null) {
            $config = $this->container->get('config');
            $this->options = array_merge($this->defaults, $config->get('cors', []));
        }

        return $this->options;
    }

    /**
     * Returns the CORS origin header
     *
     * @param Request $request
     *
     * @return array
     */
    protected function createOriginHeader(Request $request)
    {
        $header = null;
        $origin = $this->getOrigin($request);

        if ($origin) {
            $header = [
                'Access-Control-Allow-Origin' => $origin
            ];
        }

        return $header;
    }

    /**
     * Returns the CORS allowed methods header
     *
     * @return array|null
     */
    protected function createAllowedMethodsHeader()
    {
        $options = $this->getOptions();
        $header = null;

        $methods = ArrayUtils::get($options, 'methods', []);
        if (is_array($methods)) {
            $methods = implode(',', $methods);
        }

        if (!empty($methods)) {
            $header = [
                'Access-Control-Allow-Methods' => $methods
            ];
        }

        return $header;
    }

    /**
     * Returns the allowed headers header
     *
     * @param Request $request
     *
     * @return array
     */
    protected function createAllowedHeadersHeader(Request $request)
    {
        $options = $this->getOptions();
        $header = null;

        $allowedHeaders = ArrayUtils::get($options, 'headers', []);
        if (is_array($allowedHeaders)) {
            $allowedHeaders = implode(',', $allowedHeaders);
        }

        $headerName = 'Access-Control-Allow-Headers';
        // fallback to the request allowed headers
        if (empty($allowedHeaders)) {
            $allowedHeaders = $request->getHeader($headerName);
        }

        if (!empty($allowedHeaders)) {
            $header = [
                $headerName => $allowedHeaders
            ];
        }

        return $header;
    }

    /**
     * Returns exposed headers header
     *
     * @return array|null
     */
    protected function createExposedHeadersHeader()
    {
        $header = null;
        $options = $this->getOptions();

        $headers = ArrayUtils::get($options, 'exposed_headers', []);
        if (is_array($headers)) {
            $headers = implode(',', $headers);
        }

        if (!empty($headers)) {
            $header = [
                'Access-Control-Expose-Headers' => $headers
            ];
        }

        return $header;
    }

    /**
     * Returns the CORS max age header
     *
     * @return array|null
     */
    protected function createMaxAgeHeader()
    {
        $options = $this->getOptions();
        $header = null;

        $maxAge = (string) ArrayUtils::get($options, 'max_age');
        if (!empty($maxAge)) {
            $header = [
                'Access-Control-Max-Age' => $maxAge
            ];
        }

        return $header;
    }

    /**
     * Returns the credentials CORS header
     *
     * @return array|null
     */
    protected function createCredentialsHeader()
    {
        $options = $this->getOptions();
        $header = null;

        if (ArrayUtils::get($options, 'credentials') === true) {
            $header = [
                'Access-Control-Allow-Credentials' => 'true'
            ];
        }

        return $header;
    }

    /**
     * Sets a given array of headers to the response object
     *
     * @param Response $response
     * @param array $headers
     */
    protected function setHeaders(Response $response, array $headers)
    {
        $headers = array_filter($headers);
        foreach ($headers as $header) {
            $response->setHeader(key($header), current($header));
        }
    }
}
