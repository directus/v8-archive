<?php

namespace Directus\Application\Http\Middleware;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Collection\Collection;
use Directus\Config\Config;
use Directus\Util\ArrayUtils;
use Psr\Container\ContainerInterface;

class CorsMiddleware extends AbstractMiddleware
{
    /**
     * Force CORS headers processing.
     *
     * @var bool
     */
    protected $force;

    /**
     * @var array
     */
    protected $defaults = [
        'origin' => ['*'],
        'methods' => [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD',
        ],
        'headers' => [
            'Access-Control-Allow-Headers',
            'Content-Type',
            'Authorization',
        ],
        'exposed_headers' => [],
        'max_age' => 500,
        'credentials' => false,
    ];

    /**
     * @var null|Collection
     */
    protected $options;

    public function __construct(ContainerInterface $container, $force = false)
    {
        parent::__construct($container);
        $this->force = (bool) $force;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        if ($this->isEnabled()) {
            if ($request->isOptions()) {
                $this->processPreflightHeaders($request, $response);

                // These withHeader calls are a temporary hack to get around the $response here not containig the correct headers that are supposedly set above in the processpreflightheaders call.
                // TODO: Remove withHeaders calls here
                return $response
                    ->withHeader('Access-Control-Allow-Credentials', 'true')
                    ->withHeader('Access-Control-Allow-Headers', ['X-Directus-Project', 'Content-Type', 'Authorization'])
                ;
            }
            $this->processActualHeaders($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * Checks whether or not CORS is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        $options = $this->getOptions();

        return true === $this->force || $options->get('enabled', false);
    }

    /**
     * Sets the preflight response headers.
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
     * Sets the actual response headers.
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
     * Gets the header origin.
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
     * @return string
     */
    protected function getOrigin(Request $request)
    {
        $options = $this->getOptions();
        $requestOrigin = $request->getOrigin();
        $allowedOrigins = $options->get('origin', '*');

        return \Directus\cors_get_allowed_origin($allowedOrigins, $requestOrigin);
    }

    /**
     * Returns the CORS origin header.
     *
     * @return array
     */
    protected function createOriginHeader(Request $request)
    {
        $header = null;
        $origin = $this->getOrigin($request);

        if ($origin) {
            $header = [
                'Access-Control-Allow-Origin' => $origin,
            ];
        }

        return $header;
    }

    /**
     * Returns the CORS allowed methods header.
     *
     * @return null|array
     */
    protected function createAllowedMethodsHeader()
    {
        $options = $this->getOptions();
        $header = null;

        $methods = $options->get('methods', []);
        if (\is_array($methods)) {
            $methods = implode(',', $methods);
        }

        if (!empty($methods)) {
            $header = [
                'Access-Control-Allow-Methods' => $methods,
            ];
        }

        return $header;
    }

    /**
     * Returns the allowed headers header.
     *
     * @return array
     */
    protected function createAllowedHeadersHeader(Request $request)
    {
        $options = $this->getOptions();
        $header = null;

        $allowedHeaders = $options->get('headers', []);
        if (\is_array($allowedHeaders)) {
            $allowedHeaders = implode(',', $allowedHeaders);
        }

        $headerName = 'Access-Control-Allow-Headers';
        // fallback to the request allowed headers
        if (empty($allowedHeaders)) {
            $allowedHeaders = $request->getHeader($headerName);
        }

        if (!empty($allowedHeaders)) {
            $header = [
                $headerName => $allowedHeaders,
            ];
        }

        return $header;
    }

    /**
     * Returns exposed headers header.
     *
     * @return null|array
     */
    protected function createExposedHeadersHeader()
    {
        $header = null;
        $options = $this->getOptions();

        $headers = $options->get('exposed_headers', []);
        if (\is_array($headers)) {
            $headers = implode(',', $headers);
        }

        if (!empty($headers)) {
            $header = [
                'Access-Control-Expose-Headers' => $headers,
            ];
        }

        return $header;
    }

    /**
     * Returns the CORS max age header.
     *
     * @return null|array
     */
    protected function createMaxAgeHeader()
    {
        $options = $this->getOptions();
        $header = null;

        $maxAge = (string) $options->get('max_age');
        if (!empty($maxAge)) {
            $header = [
                'Access-Control-Max-Age' => $maxAge,
            ];
        }

        return $header;
    }

    /**
     * Returns the credentials CORS header.
     *
     * @return null|array
     */
    protected function createCredentialsHeader()
    {
        $options = $this->getOptions();
        $header = null;

        if (true === $options->get('credentials')) {
            $header = [
                'Access-Control-Allow-Credentials' => 'true',
            ];
        }

        return $header;
    }

    /**
     * Sets a given array of headers to the response object.
     */
    protected function setHeaders(Response $response, array $headers)
    {
        $headers = array_filter($headers);
        foreach ($headers as $header) {
            $response->setHeader(key($header), current($header));
        }
    }

    /**
     * Gets CORS options.
     *
     * @return Collection
     */
    protected function getOptions()
    {
        if (null === $this->options) {
            $config = $this->container->get('config');
            $options = [];

            if ($config instanceof Config && empty($options)) {
                $options = $config->get('cors', []);
            }

            $this->options = new Collection(
                ArrayUtils::defaults($this->defaults, $options)
            );
        }

        return $this->options;
    }
}
