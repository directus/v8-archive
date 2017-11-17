<?php

namespace Directus\Authentication;

use Directus\Authentication\Exception\InvalidInvitationCodeException;
use Directus\Authentication\Exception\InvalidTokenException;
use Directus\Authentication\Exception\InvalidUserCredentialsException;
use Directus\Authentication\Exception\UserInactiveException;
use Directus\Authentication\Exception\UserIsNotLoggedInException;
use Directus\Authentication\Exception\UserNotFoundException;
use Directus\Authentication\User\Provider\UserProviderInterface;
use Directus\Authentication\User\User;
use Directus\Authentication\User\UserInterface;
use Directus\Exception\Exception;
use Directus\Util\ArrayUtils;
use Directus\Util\DateUtils;
use Firebase\JWT\JWT;

class Provider
{
    /**
     * The user ID of the public API user.
     *
     * @var integer
     */
    const PUBLIC_USER_ID = 0;

    /**
     * Whether the user has been authenticated or not
     *
     * @var bool
     */
    protected $authenticated = false;

    /**
     * Authenticated user information
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * User Provider
     *
     * @var UserProviderInterface
     */
    protected $userProvider;

    public function __construct(UserProviderInterface $userProvider, $secretKey)
    {
        if (!is_string($secretKey)) {
            throw new Exception('secret key must be a string');
        }

        $this->userProvider = $userProvider;
        $this->user = null;
        $this->secretKey = $secretKey;
    }

    /**
     * @throws UserIsNotLoggedInException
     */
    protected function enforceUserIsAuthenticated()
    {
        if (!$this->check()) {
            throw new UserIsNotLoggedInException(__t('attempting_to_inspect_the_authenticated_user_when_a_user_is_not_authenticated'));
        }
    }

    /**
     * Signing In a user
     *
     * Creating the user token and resetting previous token
     *
     * @param array $credentials
     *
     * @return UserInterface
     *
     * @throws InvalidUserCredentialsException
     * @throws UserInactiveException
     */
    public function login(array $credentials)
    {
        $email = ArrayUtils::get($credentials, 'email');
        $password = ArrayUtils::get($credentials, 'password');

        // TODO: email and password are required

        // Verify Credentials
        if (!($user = $this->verify($email, $password))) {
            // TODO: Add exception message
            throw new InvalidUserCredentialsException();
        }

        if (!$this->isActive($user)) {
            throw new UserInactiveException();//__t('login_error_user_is_not_active'));
        }

        return $user;
    }

    /**
     * Verify if the credentials matches a user
     *
     * @param string $email
     * @param string $password
     *
     * @return UserInterface
     *
     * @throws InvalidUserCredentialsException
     */
    public function verify($email, $password)
    {
        $user = $this->user = $this->userProvider->findByEmail($email);

        // Verify that the user has an id (exists), it returns empty user object otherwise
        if (!$this->user->id || !password_verify($password, $this->user->password)) {
            // TODO: Add exception message
            throw new InvalidUserCredentialsException();
        }

        return $user;
    }

    /**
     * Checks if the user is active
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isActive(UserInterface $user)
    {
        $userProvider = $this->userProvider;

        // TODO: Cast attributes values
        return $user->get('status') == $userProvider::STATUS_ACTIVE;
    }

    /**
     * Authenticate an user using a JWT Token
     *
     * @param $token
     *
     * @return UserInterface
     *
     * @throws InvalidTokenException
     */
    public function authenticateWithToken($token)
    {
        $payload = JWT::decode($token, $this->getSecretKey(), ['HS256']);

        $conditions = [
            'id' => $payload->id,
            'group' => $payload->group
        ];

        $this->user = $this->userProvider->findWhere($conditions);
        if (!$this->user->id) {
            throw new InvalidTokenException();
        }

        return $this->user;
    }

    /**
     * Gets the user information
     *
     * @param $email
     * @param $password
     *
     * @return UserInterface
     *
     * @throws InvalidUserCredentialsException
     */
    public function getUserByAuthentication($email, $password)
    {
        // TODO: Should we prevent the private info everywhere at all cost?
        $user = $this->userProvider->findByEmail($email);
        $correct = false;

        if ($user->password) {
            $passwordHash = $user->password;
            $correct = password_verify($password, $passwordHash);
        }

        if (!$user || !$correct) {
            throw new InvalidUserCredentialsException();
        }

        $this->user = $user;

        return $user;
    }

    /**
     * Authenticate with an invitation
     *
     * NOTE: Would this be managed by the web app?
     *
     * @param $invitationCode
     *
     * @return UserInterface
     *
     * @throws InvalidInvitationCodeException
     */
    public function authenticateWithInvitation($invitationCode)
    {
        $correct = false;
        $user = $this->userProvider->findWhere(['invite_token' => $invitationCode]);

        if ($user->id) {
            $this->setLoggedUser($user);
            $correct = true;
        }

        if (!$correct) {
            throw new InvalidInvitationCodeException();
        }

        $this->user = $user;

        return $this->user;
    }

    /**
     * Force a user id to be the logged user
     *
     * TODO: Change this method name
     *
     * @param UserInterface $user The User account's ID.
     *
     * @return UserInterface
     *
     * @throws UserNotFoundException
     */
    public function setLoggedUser(UserInterface $user)
    {
        $this->authenticated = true;

        $user = $this->userProvider->find($user->getId());

        if (!$user->id) {
            throw new UserNotFoundException();
        }

        $this->user = $user;

        return $user;
    }

    /**
     * Check if a user is logged in.
     *
     * @return boolean
     */
    public function check()
    {
        return $this->authenticated;
    }

    /**
     * Retrieve metadata about the currently logged in user.
     *
     * @param null|string $attribute
     *
     * @return mixed|array Authenticated user metadata.
     *
     * @throws  \Directus\Authentication\Exception\UserIsNotLoggedInException
     */
    public function getUserAttributes($attribute = null)
    {
        $this->enforceUserIsAuthenticated();
        $user = $this->user->toArray();

        if ($attribute !== null) {
            return array_key_exists($attribute, $user) ? $user[$attribute] : null;
        }

        return $user;
    }

    /**
     * Gets authenticated user object
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Generates a new access token
     *
     * @param UserInterface $user
     *
     * @return string
     */
    public function generateToken(UserInterface $user)
    {
        // TODO: Allow customization of these values

        $algo = 'HS256';
        // TODO: Parse data types
        $payload = [
            'id' => (int)$user->getId(),
            'group' => (int)$user->get('group'),
            // Expires in 2 days
            'exp' => time() + (DateUtils::DAY_IN_SECONDS * 2)
        ];

        return JWT::encode($payload, $this->getSecretKey(), $algo);
    }

    /**
     * Run the hashing algorithm on a password and salt value.
     *
     * @param  string $password
     * @param  string $salt
     *
     * @return string
     */
    public function hashPassword($password, $salt = '')
    {
        // TODO: Create a library to hash/verify passwords up to the user which algorithm to use
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Authentication secret key
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }
}
