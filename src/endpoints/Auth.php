<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Authentication\Sso\Social;
use Directus\Services\AuthService;
use Directus\Util\ArrayUtils;

class Auth extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/authenticate', [$this, 'authenticate']);
        $app->post('/forgot_password', [$this, 'forgotPassword']);
        // $app->get('/invitation/{token}', [$this, 'acceptInvitation']);
        $app->get('/reset_password/{token}', [$this, 'resetPassword']);
        $app->post('/refresh', [$this, 'refresh']);
        $app->get('/sso', [$this, 'listSsoAuthServices']);
        $app->get('/sso/{service}', [$this, 'ssoService']);
        $app->post('/sso/{service}', [$this, 'ssoAuthenticate']);
        $app->get('/sso/{service}/callback', [$this, 'ssoServiceCallback']);
    }

    /**
     * Sign In a new user, creating a new token
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function authenticate(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->loginWithCredentials(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('password')
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * Sends a user a token to reset its password
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function forgotPassword(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $authService->sendResetPasswordToken(
            $request->getParsedBodyParam('email')
        );

        $responseData = [];
        $response = $response->withStatus(204);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function resetPassword(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $authService->resetPasswordWithToken(
            $request->getAttribute('token')
        );

        $responseData = [];
        $response = $response->withStatus(204);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * Refresh valid JWT token
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function refresh(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->refreshToken(
            $request->getParsedBodyParam('token')
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function listSsoAuthServices(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');
        /** @var Social $externalAuth */
        $externalAuth = $this->container->get('external_auth');

        $services = [];
        foreach ($externalAuth->getAll() as $name => $provider) {
            $services[] = $authService->getSsoInfo($name);
        }

        $responseData = ['data' => $services];

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function ssoService(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->getAuthenticationRequestInfo(
            $request->getAttribute('service')
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function ssoAuthenticate(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->authenticateWithSsoCode(
            $request->getAttribute('service'),
            $request->getParsedBody() ?: []
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function ssoServiceCallback(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->handleAuthenticationRequestCallback(
            $request->getAttribute('service')
        );

        return $this->responseWithData($request, $response, $responseData);
    }
}
