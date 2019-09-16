<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use function Directus\array_get;
use function Directus\get_directus_setting;
use function Directus\get_request_authorization_token;
use function Directus\encrypt_static_token;
use function Directus\decrypt_static_token;
use Directus\Authentication\Exception\UserWithEmailNotFoundException;
use Directus\Authentication\Sso\Social;
use Directus\Services\AuthService;
use Directus\Services\UserSessionService;
use Directus\Util\ArrayUtils;
use Slim\Http\Cookies;
use Directus\Database\TableGateway\DirectusUserSessionsTableGateway;

class Auth extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/authenticate', [$this, 'authenticate']);
        $app->post('/password/request', [$this, 'forgotPassword']);
        $app->post('/sessions/start', [$this, 'startSession']);
        $app->post('/sessions/stop', [$this, 'stopSession']);
        $app->post('/sessions/kill/{user}', [$this, 'killAllSession']);
        $app->post('/sessions/kill/{user}/{id}', [$this, 'killUserSession']);
        $app->get('/password/reset/{token}', [$this, 'resetPassword']);
        $app->post('/refresh', [$this, 'refresh']);
        $app->get('/sso', [$this, 'listSsoAuthServices']);
        $app->post('/sso/access_token', [$this, 'ssoAccessToken']);
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
        $this->validateRequestPayload($request);
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->loginWithCredentials(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('password'),
            $request->getParsedBodyParam('otp')
        );

        if(isset($responseData['data']) && isset($responseData['data']['user'])){
            $expirationMinutes =  get_directus_setting('auto_sign_out');
            $expiry = new \DateTimeImmutable('now + '.$expirationMinutes.'minutes');
            $userSessionService = new UserSessionService($this->container);
            $userSessionService->create([
                'user' => $responseData['data']['user']['id'],
                'token' => $responseData['data']['token'],
                'token_type' => DirectusUserSessionsTableGateway::TOKEN_JWT,
                'token_expired_at' => $expiry->format('Y-m-d H:i:s')
            ]);
            unset($responseData['data']['user']);
        }

        return $this->responseWithData($request, $response, $responseData);
    }
    
    /**
     * Start a session of user
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function startSession(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->loginWithCredentials(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('password'),
            $request->getParsedBodyParam('otp'),
            true
        );
        if(isset($responseData['data']) && isset($responseData['data']['user'])){
            $authorizationTokenObject = get_request_authorization_token($request);
            $expirationMinutes =  get_directus_setting('auto_sign_out');
            $expiry = new \DateTimeImmutable('now + '.$expirationMinutes.'minutes');
            $userSessionService = new UserSessionService($this->container);
            if(!empty($authorizationTokenObject['token'])){
                $accessToken = decrypt_static_token($authorizationTokenObject['token']);
                $userSessionObject = $userSessionService->find(['token' => $accessToken]);
                $sessionToken = $userSessionObject['token'];
            }else{
                $userSession = $userSessionService->create([
                    'user' => $responseData['data']['user']['id'],
                    'token' => $responseData['data']['user']['token'],
                    'token_type' => DirectusUserSessionsTableGateway::TOKEN_COOKIE,
                    'token_expired_at' => $expiry->format('Y-m-d H:i:s')
                ]);
                $sessionToken = $responseData['data']['user']['token']."-".$userSession;
                $userSessionService->update($userSession,['token' => $sessionToken]);
            }
            $cookie = new Cookies();
            $cookie->set('session',['value' => encrypt_static_token($sessionToken),'expires' =>$expiry->format(\DateTime::COOKIE),'path'=>'/','httponly' => true]);
            $response =  $response->withAddedHeader('Set-Cookie',$cookie->toHeaders());
        }

        return $this->responseWithData($request, $response, []);
    }

    /**
     * Stop the session of user
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function stopSession(Request $request, Response $response)
    {
        $authorizationTokenObject = get_request_authorization_token($request);
        if(!empty($authorizationTokenObject['token']) && $authorizationTokenObject['type'] == DirectusUserSessionsTableGateway::TOKEN_COOKIE){
            $accessToken = decrypt_static_token($authorizationTokenObject['token']);
            $userSessionService = new UserSessionService($this->container);
            $userSessionService->destroy(['token' => $accessToken]);
        }
        return $this->responseWithData($request, $response, []);
    }

    /**
     * Kill all sessions of user
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function killAllSession(Request $request, Response $response)
    {
        $userSessionService = new UserSessionService($this->container);
        $responseData = $userSessionService->destroy([
            'user' => $request->getAttribute('user')
        ]);
        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * Kill particular session of user
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function killUserSession(Request $request, Response $response)
    {
        $userSessionService = new UserSessionService($this->container);
        $responseData = $userSessionService->destroy([
            'id' => $request->getAttribute('id'),
            'user' => $request->getAttribute('user')
        ]);
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
        $this->validateRequestPayload($request);
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        try {
            $authService->sendResetPasswordToken(
                $request->getParsedBodyParam('email')
            );
        } catch (\Exception $e) {
            $this->container->get('logger')->error($e);
        }

        return $this->responseWithData($request, $response, []);
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

        return $this->responseWithData($request, $response, []);
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
            $services[] = $authService->getSsoBasicInfo($name);
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
        $origin = $request->getReferer();
        $config = $this->container->get('config');
        $corsOptions = $config->get('cors', []);
        $allowedOrigins = ArrayUtils::get($corsOptions, 'origin');
        $session = $this->container->get('session');

        $responseData = $authService->getAuthenticationRequestInfo(
            $request->getAttribute('service')
        );

        if (\Directus\cors_is_origin_allowed($allowedOrigins, $origin)) {
            if (is_array($origin)) {
                $origin = array_shift($origin);
            }

            $session->set('sso_origin_url', $origin);
            $response = $response->withRedirect(array_get($responseData, 'data.authorization_url'));
        }

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
     *
     * @throws \Exception
     */
    public function ssoServiceCallback(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');
        $session = $this->container->get('session');
        // TODO: Implement a pull method
        $redirectUrl = $session->get('sso_origin_url');
        $session->remove('sso_origin_url');

        $responseData = [];
        $urlParams = [];
        try {
            $responseData = $authService->handleAuthenticationRequestCallback(
                $request->getAttribute('service'),
                !!$redirectUrl
            );

            $urlParams['request_token'] = array_get($responseData, 'data.token');
        } catch (\Exception $e) {
            if (!$redirectUrl) {
                throw $e;
            }

            if ($e instanceof UserWithEmailNotFoundException) {
                $urlParams['attributes'] = $e->getAttributes();
            }

            $urlParams['code'] = ($e instanceof \Directus\Exception\Exception) ? $e->getErrorCode() : 0;
            $urlParams['error'] = true;
        }

        if ($redirectUrl) {
            $redirectQueryString = parse_url($redirectUrl, PHP_URL_QUERY);
            $redirectUrlParts = explode('?', $redirectUrl);
            $redirectUrl = $redirectUrlParts[0];
            $redirectQueryParams = parse_str($redirectQueryString);
            if (is_array($redirectQueryParams)) {
                $urlParams = array_merge($redirectQueryParams, $urlParams);
            }

            $response = $response->withRedirect($redirectUrl . '?' . http_build_query($urlParams));
        }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function ssoAccessToken(Request $request, Response $response)
    {
        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $responseData = $authService->authenticateWithSsoRequestToken(
            $request->getParsedBodyParam('request_token')
        );

        return $this->responseWithData($request, $response, $responseData);
    }
}
