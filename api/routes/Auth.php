<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Exception\BadRequestException;
use Directus\Exception\Exception;
use Directus\Services\AuthService;
use Directus\Util\DateUtils;
use Directus\Validator\Validator;

class Auth extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/authenticate', [$this, 'authenticate']);
        $app->post('/refresh', [$this, 'refresh']);
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
        // throws an exception if the constraints are not met
        $this->validateRequest($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $user = $authService->loginWithCredentials(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('password')
        );

        $authenticated = false;

        // ------------------------------
        // Check if group needs whitelist
        $dbConnection = $this->container->get('database');
        $groupId = $user->get('group');
        $directusGroupsTableGateway = new DirectusGroupsTableGateway($dbConnection, null);
        if (!$directusGroupsTableGateway->acceptIP($groupId, $request->getAttribute('ip_address'))) {
            $responseData = [
                'error' => [
                    'message' => 'Request not allowed from IP address',
                ]
            ];
        } else {
            $responseData = [
                'data' => [
                    'token' => $authService->generateToken($user)
                ]
            ];
            $authenticated = true;
        }

        if ($authenticated) {
            $hookEmitter = $this->container->get('hook_emitter');
            $hookEmitter->run('directus.authenticated', [$user]);
            // TODO: Move to the hook above
            $Activity = new DirectusActivityTableGateway($dbConnection, null);
            $Activity->recordLogin($user->get('id'));
        }

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
        $this->validateRequest($request, [
            'token' => 'required'
        ]);

        /** @var AuthService $authService */
        $authService = $this->container->get('services')->get('auth');

        $token = $authService->refreshToken(
            $request->getParsedBodyParam('token')
        );

        return $this->responseWithData($request, $response, ['data' => ['token' => $token]]);
    }

    public function resetPassword($token)
    {
        $app = $this->app;
        $auth = $app->container->get('auth');
        $ZendDb = $app->container->get('zenddb');

        $DirectusUsersTableGateway = new DirectusUsersTableGateway($ZendDb, null);
        $user = $DirectusUsersTableGateway->findOneBy('reset_token', $token);

        if (!$user) {
            $app->halt(200, __t('password_reset_incorrect_token'));
        }

        $expirationDate = new \DateTime($user['reset_expiration'], new \DateTimeZone('UTC'));
        if (DateUtils::hasPassed($expirationDate)) {
            $app->halt(200, __t('password_reset_expired_token'));
        }

        $password = StringUtils::randomString();
        $set = [];
        // @NOTE: this is not being used for hashing the password anymore
        $set['salt'] = StringUtils::randomString();
        $set['password'] = $auth->hashPassword($password, $set['salt']);
        $set['reset_token'] = '';

        // Skip ACL
        $DirectusUsersTableGateway = new \Zend\Db\TableGateway\TableGateway('directus_users', $ZendDb);
        $affectedRows = $DirectusUsersTableGateway->update($set, ['id' => $user['id']]);

        if (1 !== $affectedRows) {
            $app->halt(200, __t('password_reset_error'));
        }

        send_forgot_password_email($user, $password);

        $app->halt(200, __t('password_reset_new_temporary_password_sent'));
    }

    public function forgotPassword()
    {
        $app = $this->app;
        $ZendDb = $app->container->get('zenddb');

        $email = $app->request()->post('email');
        if (!isset($email)) {
            return $this->app->response([
                'success' => false,
                'error' => [
                    'message' => __t('invalid_email')
                ]
            ]);
        }

        $DirectusUsersTableGateway = new DirectusUsersTableGateway($ZendDb, null);
        $user = $DirectusUsersTableGateway->findOneBy('email', $email);

        if (false === $user) {
            return $this->app->response([
                'success' => false,
                'error' => [
                    'message' => __t('password_forgot_no_account_found')
                ]
            ]);
        }

        $set = [];
        $set['reset_token'] = StringUtils::randomString(30);
        $set['reset_expiration'] = DateUtils::inDays(2);

        // Skip ACL
        $DirectusUsersTableGateway = new \Zend\Db\TableGateway\TableGateway('directus_users', $ZendDb);
        $affectedRows = $DirectusUsersTableGateway->update($set, ['id' => $user['id']]);

        if (1 !== $affectedRows) {
            return $this->app->response([
                'success' => false
            ]);
        }

        send_reset_password_email($user, $set['reset_token']);

        return $this->app->response([
            'success' => true
        ]);
    }

    public function permissions()
    {
        $acl = $this->app->container->get('acl');

        return $this->app->response([
            'data' => $acl->getGroupPrivileges()
        ]);
    }
}
