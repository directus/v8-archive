<?php

namespace Directus\Application\Http\Middlewares;

use Directus\Application\Container;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Authentication\Exception\UserIsNotLoggedInException;
use Directus\Authentication\Provider;
use Directus\Authentication\User\User;
use Directus\Authentication\User\UserInterface;
use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Database\TableGateway\DirectusPrivilegesTableGateway;
use Directus\Database\TableGatewayFactory;
use Directus\Permissions\Acl;
use Directus\Services\AuthService;

class AuthenticationMiddleware extends AbstractMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     *
     * @throws InvalidUserCredentialsException
     * @throws UserIsNotLoggedInException
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        // TODO: Improve this, move back from api.php to make the table gateway work with its dependency
        $container = $this->container;
        \Directus\Database\TableSchema::setAclInstance($container->get('acl'));
        \Directus\Database\TableSchema::setConnectionInstance($container->get('database'));
        \Directus\Database\TableSchema::setConfig($container->get('config'));
        BaseTableGateway::setHookEmitter($container->get('hook_emitter'));
        BaseTableGateway::setHookEmitter($container->get('hook_emitter'));
        BaseTableGateway::setContainer($container);
        TableGatewayFactory::setContainer($container);

        $container['app.settings'] = function (Container $container) {
            $dbConnection = $container->get('database');
            $DirectusSettingsTableGateway = new \Zend\Db\TableGateway\TableGateway('directus_settings', $dbConnection);
            $rowSet = $DirectusSettingsTableGateway->select();

            $settings = [];
            foreach ($rowSet as $setting) {
                $settings[$setting['scope']][$setting['key']] = $setting['value'];
            }

            return $settings;
        };

        // TODO: Whitelist path
        // TODO: Check whitelist
        $whitelisted = ['auth/login', 'auth/refresh'];
        if (in_array($request->getUri()->getPath(), $whitelisted)) {
            return $next($request, $response);
        }

        $authenticated = $this->authenticate($request);
        $publicGroupId = $this->getPublicGroupId();
        if (!$authenticated && !$publicGroupId) {
            throw new UserIsNotLoggedInException();
        }

        // =============================================================================
        // Set authenticated user permissions
        // =============================================================================
        $hookEmitter = $this->container->get('hook_emitter');

        if (!$authenticated && $publicGroupId) {
            // NOTE: 0 will not represent a "guest" or the "public" user
            // To prevent the issue where user column on activity table can't be null
            $user = new User([
                'id' => 0,
                'group' => $publicGroupId
            ]);
        } else {
            $user = $this->container->get('auth')->getUser();
        }

        // TODO: Set if the authentication was a public or not? options array
        $hookEmitter->run('directus.authenticated', [$user]);
        $hookEmitter->run('directus.authenticated.token', [$user]);

        // Reload all user permissions
        // At this point ACL has run and loaded all permissions
        // This behavior works as expected when you are logged to the CMS/Management
        // When logged through API we need to reload all their permissions
        $dbConnection = $this->container->get('database');
        /** @var Acl $acl */
        $acl = $this->container->get('acl');
        $privilegesTable = new DirectusPrivilegesTableGateway($dbConnection, $acl);
        $acl->setGroupPrivileges($privilegesTable->getGroupPrivileges($user->group));
        // TODO: Adding an user should auto set its ID and GROUP
        // TODO: User data should be casted to its data type
        // TODO: Make sure that the group is not empty
        $acl->setUserId($user->id);
        $acl->setGroupId($user->group);
        if (!$authenticated && $publicGroupId) {
            $acl->setPublic($publicGroupId);
        }

        // Set full permission to Admin
        if ($acl->isAdmin()) {
            $acl->setTablePrivileges('*', $acl::PERMISSION_FULL);
        }

        return $next($request, $response);
    }

    /**
     * Tries to authenticate the user based on the HTTP Request
     *
     * @param Request $request
     *
     * @return UserInterface
     */
    protected function authenticate(Request $request)
    {
        $authenticate = false;
        $authToken = $this->getAuthToken($request);

        if ($authToken) {
            /** @var AuthService $authService */
            $authService = $this->container->get('services')->get('auth');

            $authenticate = $authService->authenticateWithToken($authToken);
        }

        return $authenticate;
    }

    /**
     * Gets the authentication token from the request
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getAuthToken(Request $request)
    {
        $authToken = null;

        if ($request->getParam('access_token')) {
            $authToken = $request->getParam('access_token');
        } elseif ($request->hasHeader('Php-Auth-User')) {
            $authUser = $request->getHeader('Php-Auth-User');
            $authPassword = $request->getHeader('Php-Auth-Pw');

            if (is_array($authUser)) {
                $authUser = array_shift($authUser);
            }

            if (is_array($authPassword)) {
                $authPassword = array_shift($authPassword);
            }

            if ($authUser && empty($authPassword)) {
                $authToken = $authUser;
            }
        } elseif ($request->hasHeader('Authorization')) {
            $authorizationHeader = $request->getHeader('Authorization');

            // If there's multiple Authorization header, pick first, ignore the rest
            if (is_array($authorizationHeader)) {
                $authorizationHeader = array_shift($authorizationHeader);
            }

            if (is_string($authorizationHeader) && preg_match("/Bearer\s+(.*)$/i", $authorizationHeader, $matches)) {
                $authToken = $matches[1];
            }
        }

        return $authToken;
    }

    /**
     * Gets the public group id if exists
     *
     * @return int|null
     */
    protected function getPublicGroupId()
    {
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');

        $directusGroupsTableGateway = new DirectusGroupsTableGateway($dbConnection, $acl);
        $publicGroup = $directusGroupsTableGateway->select(['name' => 'public'])->current();

        $groupId = null;
        if ($publicGroup) {
            $groupId = $publicGroup['id'];
        }

        return $groupId;
    }
}
