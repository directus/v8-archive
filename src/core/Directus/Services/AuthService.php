<?php

namespace Directus\Services;

use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Authentication\Provider;
use Directus\Authentication\User\UserInterface;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Exception\UnauthorizedException;
use Directus\Util\JWTUtils;

class AuthService extends AbstractService
{
    /**
     * Gets the user token using the authentication email/password combination
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     *
     * @throws UnauthorizedException
     */
    public function loginWithCredentials($email, $password)
    {
        // throws an exception if the constraints are not met
        $payload = [
            'email' => $email,
            'password' => $password
        ];
        $constraints = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        $this->validate($payload, $constraints);

        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        /** @var UserInterface $user */
        $user = $auth->login([
            'email' => $email,
            'password' => $password
        ]);

        // ------------------------------
        // Check if group needs whitelist
        /** @var DirectusGroupsTableGateway $groupTableGateway */
        $groupTableGateway = $this->createTableGateway('directus_groups', false);
        if (!$groupTableGateway->acceptIP($user->getGroupId(), get_request_ip())) {
            throw new UnauthorizedException('Request not allowed from IP address');
        }

        $hookEmitter = $this->container->get('hook_emitter');
        $hookEmitter->run('directus.authenticated', [$user]);

        // TODO: Move to the hook above
        /** @var DirectusActivityTableGateway $activityTableGateway */
        $activityTableGateway = $this->createTableGateway('directus_activity', false);
        $activityTableGateway->recordLogin($user->get('id'));

        return [
            'data' => [
                'token' => $this->generateToken($user)
            ]
        ];
    }

    /**
     * @param $token
     *
     * @return UserInterface
     */
    public function authenticateWithToken($token)
    {
        if (JWTUtils::isJWT($token)) {
            $authenticated = $this->getAuthProvider()->authenticateWithToken($token);
        } else {
            $authenticated = $this->getAuthProvider()->authenticateWithPrivateToken($token);
        }

        return $authenticated;
    }

    /**
     * Generates JWT Token
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function generateToken(UserInterface $user)
    {
        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        return $auth->generateToken($user);
    }

    public function refreshToken($token)
    {
        $this->validate([
            'token' => $token
        ], [
            'token' => 'required'
        ]);

        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        return ['data' => ['token' => $auth->refreshToken($token)]];
    }

    /**
     * @return Provider
     */
    protected function getAuthProvider()
    {
        return $this->container->get('auth');
    }
}
