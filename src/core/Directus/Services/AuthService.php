<?php

namespace Directus\Services;

use Directus\Authentication\Exception\ExpiredResetPasswordToken;
use Directus\Authentication\Exception\InvalidResetPasswordTokenException;
use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Authentication\Exception\UserNotFoundException;
use Directus\Authentication\Provider;
use Directus\Authentication\User\UserInterface;
use Directus\Database\TableGateway\DirectusActivityTableGateway;
use Directus\Database\TableGateway\DirectusGroupsTableGateway;
use Directus\Exception\BadRequestException;
use Directus\Exception\UnauthorizedException;
use Directus\Util\JWTUtils;
use Directus\Util\StringUtils;

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
        $this->validateCredentials($email, $password);

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
                'token' => $this->generateAuthToken($user)
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
    public function generateAuthToken(UserInterface $user)
    {
        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        return $auth->generateAuthToken($user);
    }

    public function sendResetPasswordToken($email, $password)
    {
        $this->validateCredentials($email, $password);

        /** @var Provider $auth */
        $auth = $this->container->get('auth');
        $user = $auth->findUserWithCredentials($email, $password);

        if (!$user) {
            throw new InvalidUserCredentialsException();
        }

        $resetToken = $auth->generateResetPasswordToken($user);

        send_forgot_password_email($user->toArray(), $resetToken);
    }

    public function resetPasswordWithToken($token)
    {
        if (!JWTUtils::isJWT($token)) {
            throw new InvalidResetPasswordTokenException($token);
        }

        if (JWTUtils::hasExpired($token)) {
            throw new ExpiredResetPasswordToken($token);
        }

        $payload = JWTUtils::getPayload($token);

        /** @var Provider $auth */
        $auth = $this->container->get('auth');
        $userProvider = $auth->getUserProvider();
        $user = $userProvider->find($payload->id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $newPassword = StringUtils::randomString(16);
        $userProvider->update($user, [
            'password' => $auth->hashPassword($newPassword)
        ]);

        send_reset_password_email($user->toArray(), $newPassword);
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

    /**
     * Validates email+password credentials
     *
     * @param $email
     * @param $password
     *
     * @throws BadRequestException
     */
    protected function validateCredentials($email, $password)
    {
        $payload = [
            'email' => $email,
            'password' => $password
        ];
        $constraints = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        // throws an exception if the constraints are not met
        $this->validate($payload, $constraints);
    }
}
