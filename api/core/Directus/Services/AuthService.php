<?php

namespace Directus\Services;

use Directus\Authentication\Provider;
use Directus\Authentication\User\UserInterface;
use Directus\Util\JWTUtils;

class AuthService extends AbstractService
{
    /**
     * Gets the user token using the authentication email/password combination
     *
     * @param string $email
     * @param string $password
     *
     * @return UserInterface
     */
    public function loginWithCredentials($email, $password)
    {
        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        $user = null;
        if ($email && $password) {
            /** @var UserInterface $user */
            $user = $auth->login([
                'email' => $email,
                'password' => $password
            ]);
        }

        return $user;
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
        /** @var Provider $auth */
        $auth = $this->container->get('auth');

        return $auth->refreshToken($token);
    }

    /**
     * @return Provider
     */
    protected function getAuthProvider()
    {
        return $this->container->get('auth');
    }
}
