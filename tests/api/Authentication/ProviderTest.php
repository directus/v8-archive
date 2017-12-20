<?php

use Directus\Authentication\Provider as Auth;

class ProviderTest extends PHPUnit_Framework_TestCase
{
    const DATA_USER_INACTIVE = 0;

    /**
     * Authentication provider
     *
     * @var \Directus\Authentication\Provider
     */
    protected $provider;

    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var \Directus\Database\TableGateway\BaseTableGateway
     */
    protected $table;

    /**
     * @var \Directus\Authentication\User\Provider\UserProviderInterface
     */
    protected $userProvider;

    protected $secretKey = 'secret-key';

    public function setUp()
    {
        $this->adapter = get_mock_adapter($this, ['id' => 1, 'email' => 'admin@getdirectus.com']);
        $this->table = $this->getTableGateway($this->adapter);
        $this->userProvider = $this->getUserProvider($this->table);

        $this->provider = new Auth($this->userProvider, $this->secretKey);
    }

    public function tearUp()
    {
        $this->adapter = $this->table = $this->userProvider = $this->provider = null;
    }

    /**
     * @expectedException \Directus\Exception\Exception
     */
    public function testInvalidSecretKey()
    {
        new Auth($this->userProvider, 123);
    }

    public function testSecretKey()
    {
        $auth = $this->provider;

        $this->assertSame($this->secretKey, $auth->getSecretKey());
    }

    /**
     * @expectedException \Directus\Authentication\Exception\UserIsNotLoggedInException
     */
    public function testAuthenticationEnforcement()
    {
        $this->provider->getUserAttributes();
    }

    /**
     * @expectedException \Directus\Authentication\Exception\UserIsNotLoggedInException
     */
    public function testAuthenticationEnforcement2()
    {
        $this->provider->getUserAttributes('email');
    }

    /**
     * @expectedException \Directus\Authentication\Exception\UserInactiveException
     */
    public function testInactiveUser()
    {
        $auth = $this->getAuth(static::DATA_USER_INACTIVE);

        $auth->login(['email' => 'admin@getdirectus.com', 'password' => 'secret-password']);
    }

    /**
     * @expectedException \Directus\Authentication\Exception\InvalidUserCredentialsException
     */
    public function testInvalidCredentials()
    {
        $auth = $this->provider;
        $result = $auth->login(['email' => 'admin@getdirectus.com', 'password' => 'password']);
    }

    public function testSuccessfulLogin()
    {
        $email = 'admin@getdirectus.com';
        $password = 'secret-password';
        $auth = $this->provider;
        $result = $auth->login(['email' => $email, 'password' => $password]);

        $this->assertInstanceOf(\Directus\Authentication\User\UserInterface::class, $result);
        $this->assertInstanceOf(\Directus\Authentication\User\UserInterface::class, $auth->getUser());
    }

    public function testAttributes()
    {
        $email = 'admin@getdirectus.com';
        $password = 'secret-password';
        $auth = $this->provider;
        $result = $auth->login(['email' => $email, 'password' => $password]);

        $this->assertSame(1, $auth->getUserAttributes('id'));

        $data = $auth->getUserAttributes();
        $this->assertSame(1, $data['id']);
        $this->assertSame($auth->getUserAttributes('id'), $data['id']);
    }

    public function testUserObject()
    {
        $email = 'admin@getdirectus.com';
        $password = 'secret-password';
        $auth = $this->provider;
        $result = $auth->login(['email' => $email, 'password' => $password]);

        $this->assertSame($result, $auth->getuser());
        $this->assertSame($email, $auth->getUserAttributes('email'));
        $this->assertSame($email, $result->getEmail());
        $this->assertSame(1, $result->getId());
        $this->assertSame($email, $result->get('email'));
    }

    /**
     * @expectedException \Directus\Authentication\Exception\UnknownUserAttributeException
     */
    public function testUnknownUserProperty()
    {
        $email = 'admin@getdirectus.com';
        $password = 'secret-password';
        $auth = $this->provider;
        $result = $auth->login(['email' => $email, 'password' => $password]);

        $fullName = $result->full_name;
    }

    public function testAuthenticateWithToken()
    {
        $email = 'admin@getdirectus.com';
        $password = 'secret-password';
        $user = $this->provider->login(['email' => $email, 'password' => $password]);

        $token = $this->provider->generateToken($user);

        $auth = $this->getAuth();
        $result = $auth->authenticateWithToken($token);

        $this->assertSame($email, $result->getEmail());
    }

    /**
     * @expectedException \Directus\Authentication\Exception\InvalidTokenException
     */
    public function testAuthenticateWithInvalidToken()
    {
        $token = $this->generateInvalidPayloadToken();
        $userProvider = $this->getMockUserProvider($this->table);
        $this->addEmptyUserToMockUserProvider($userProvider);

        $auth = new Auth($userProvider, $this->secretKey);
        $result = $auth->authenticateWithToken($token);
    }

    /**
     * @expectedException \Firebase\JWT\ExpiredException
     */
    public function testAuthenticateWithExpiredToken()
    {
        $token = $this->generateExpiredToken();

        // $auth = new Auth($this->getMockUserProvider($this->table), $this->secretKey);
        $this->provider->authenticateWithToken($token);
    }

    public function testRefreshToken()
    {
        $token = $this->generateToken();
        $payload = $this->decodeToken($token);

        $newToken = $this->provider->refreshToken($token);
        $newPayload = $this->decodeToken($newToken);

        $this->assertTrue($newPayload->exp > $payload->exp);
    }

    /**
     * @expectedException \Firebase\JWT\ExpiredException
     */
    public function testRefreshTokenWithExpiredToken()
    {
        $token = $this->generateExpiredToken();
        $newToken = $this->provider->refreshToken($token);
    }

    public function testPassword()
    {
        $hashedPassword = $this->provider->hashPassword('secret-password');

        $this->assertTrue(password_verify('secret-password', $hashedPassword));
    }

    public function testForceUserLogin()
    {
        $user = new \Directus\Authentication\User\User($this->getData());

        $auth = $this->getAuth();
        $auth->forceUserLogin($user);

        $this->assertTrue($auth->check());
        $this->assertSame($user, $auth->getUser());
        $this->assertSame($user->getEmail(), $auth->getUserAttributes('email'));
    }

    /**
     * @expectedException \Directus\Authentication\Exception\UserNotFoundException
     */
    public function testMissingIdForceUserLogin()
    {
        $user = new \Directus\Authentication\User\User();

        $auth = $this->getAuth();
        $auth->forceUserLogin($user);
    }

    public function testInvitationToken()
    {
        $token = 'invitation-token';

        $auth = $this->getAuth();

        $user = $auth->authenticateWithInvitation($token);

        $this->assertInstanceOf(\Directus\Authentication\User\UserInterface::class, $user);
        $this->assertSame(1, $user->getId());
    }

    /**
     * @expectedException \Directus\Authentication\Exception\InvalidInvitationCodeException
     */
    public function testInvalidInvitationToken()
    {
        $token = 'invalid-token';

        $userProvider = $this->getMockUserProvider($this->table);
        $this->addNotFoundUserToMockUserProvider($userProvider);

        $auth = new Auth($userProvider, $this->secretKey);

        $user = $auth->authenticateWithInvitation($token);
    }

    //
    // public function testLogin()
    // {
    //     $auth = $this->provider;
    //     $password = '123456';
    //     $salt = 'salt';
    //
    //     $passwordHashed = $auth->hashPassword($password, $salt);
    //     $result = $auth->login(1, $passwordHashed, $salt, $password, true);
    //     $this->assertTrue($result);
    // }
    //
    // public function testSuccessAuthenticationByUser()
    // {
    //     $password = '123456';
    //     $salt = 'salt';
    //
    //     $adapter = get_mock_adapter($this, ['result_data' => [
    //         'password' => $this->provider->hashPassword($password, $salt)
    //     ]]);
    //
    //     $table = $this->getTableGateway($adapter);
    //     $auth = new \Directus\Authentication\Provider($table, $this->session);
    //
    //     $user = $auth->getUserByAuthentication('email@mail.com', '123456');
    //     $this->assertNotEquals($user, false);
    //
    //     $this->assertTrue($auth->verify('email@mail.com', '123456'));
    // }
    //
    // public function testFailAuthenticationByUser()
    // {
    //     $password = 'secret';
    //     $salt = 'salt';
    //
    //     $adapter = get_mock_adapter($this, ['result_data' => [
    //         'password' => $this->provider->hashPassword($password, $salt)
    //     ]]);
    //
    //     $table = $this->getTableGateway($adapter);
    //     $auth = new \Directus\Authentication\Provider($table, $this->session);
    //
    //     $user = $auth->getUserByAuthentication('email@mail.com', '123456');
    //     $this->assertFalse($user);
    //
    //     $this->assertFalse($auth->verify('email@mail.com', '123456'));
    // }
    //
    // /**
    //  * @expectedException \Directus\Authentication\Exception\UserAlreadyLoggedInException
    //  */
    // public function testLoginSuccessfulTwice()
    // {
    //     $this->testLogin();
    //     $this->testLoginLegacyPassword();
    // }
    //
    // /**
    //  * @expectedException \InvalidArgumentException
    //  */
    // public function testRefreshCallable()
    // {
    //     $this->provider->setUserCacheRefreshProvider(true);
    // }
    //
    // /**
    //  * @expectedException \RuntimeException
    //  */
    // public function testGetUserRecordException()
    // {
    //     $this->testLogin();
    //     $this->provider->getUserRecord();
    // }
    //
    // public function testGetUserRecord()
    // {
    //     $this->testLogin();
    //
    //     $userData = [
    //         'id' => 1,
    //         'email' => 'admin@demo.local'
    //     ];
    //
    //     $this->provider->setUserCacheRefreshProvider(function($id) use ($userData) {
    //        return $userData;
    //     });
    //
    //     $data = $this->provider->getUserRecord();
    //     $this->assertSame($data, $userData);
    // }
    //
    // public function testExpireCachedUserRecord()
    // {
    //     $auth = $this->provider;
    //     $auth->expireCachedUserRecord();
    //     $this->assertNull($this->session->get($auth::USER_RECORD_CACHE_SESSION_KEY));
    // }
    //
    // /**
    //  * @expectedException \Directus\Authentication\Exception\UserIsntLoggedInException
    //  */
    // public function testGetUserInfoException()
    // {
    //     $session = get_array_session();
    //     $provider = new Auth($this->table, $session, $this->prefix);
    //
    //     $provider->getUserInfo();
    // }
    //
    // public function testGetUserInfo()
    // {
    //     $this->testLogin();
    //     $this->assertInternalType('array', $this->provider->getUserInfo());
    //
    //     $this->assertSame(1, $this->provider->getUserInfo('id'));
    // }
    //
    // public function testLoggedIn()
    // {
    //     $this->assertFalse($this->provider->loggedIn());
    //     $this->testLogin();
    //     $this->assertTrue($this->provider->loggedIn());
    //
    //     $adapter = get_mock_adapter($this, ['result_count' => 1]);
    //     $table = $this->getTableGateway($adapter);
    //     $session = get_array_session();
    //
    //     $provider = new Auth($table, $session, $this->prefix);
    //
    //     $session->set($this->prefix . $provider->getSessionKey(), [
    //         'id' => 1,
    //         'access_token' => 'accessTokenTest'
    //     ]);
    //
    //     $this->assertTrue($provider->loggedIn());
    // }
    //
    // public function testSetLoggedUser()
    // {
    //     $this->provider->setLoggedUser(2, true);
    //
    //     $this->assertSame(2, $this->provider->getUserInfo('id'));
    //     $this->assertTrue($this->provider->loggedIn());
    // }
    //
    // /**
    //  * @expectedException \Directus\Authentication\Exception\UserIsntLoggedInException
    //  */
    // public function testLogoutException()
    // {
    //     $session = get_array_session();
    //     $provider = new Auth($this->table, $session, $this->prefix);
    //
    //     $provider->logout(true);
    // }
    //
    // public function testLogout()
    // {
    //     $this->testLogin();
    //     $this->provider->logout(true);
    //     $session = $this->session->get($this->provider->getSessionKey());
    //     $this->assertEmpty($session);
    // }
    //

    protected function generateToken()
    {
        return \Firebase\JWT\JWT::encode(['id' => 10, 'group' => 2, 'exp' => time() + 60], $this->secretKey, 'HS256');
    }

    protected function generateInvalidPayloadToken()
    {
        return \Firebase\JWT\JWT::encode(['id' => 10, 'group' => 2, 'exp' => time() * 2], $this->secretKey, 'HS256');
    }

    protected function generateExpiredToken()
    {
        return \Firebase\JWT\JWT::encode(['id' => 10, 'group' => 2, 'exp' => time() - 2], $this->secretKey, 'HS256');
    }

    protected function decodeToken($token)
    {
        return \Firebase\JWT\JWT::decode($token, $this->secretKey, ['HS256']);
    }

    /**
     * @param null $type
     *
     * @return Auth
     */
    protected function getAuth($type = null)
    {
        return new Auth(
            new \Directus\Authentication\User\Provider\UserTableGatewayProvider(
                $this->getTableGateway($this->adapter, $type)
            ),
            $this->secretKey
        );
    }

    protected function getUserProvider(\Directus\Database\TableGateway\BaseTableGateway $tableGateway)
    {
        return new \Directus\Authentication\User\Provider\UserTableGatewayProvider($tableGateway);
    }

    /**
     * @param \Directus\Database\TableGateway\BaseTableGateway $tableGateway
     *
     * @return PHPUnit_Framework_MockObject_MockObject|\Directus\Authentication\User\Provider\UserProviderInterface
     */
    protected function getMockUserProvider(\Directus\Database\TableGateway\BaseTableGateway $tableGateway)
    {
        $userProvider = create_mock(
            $this,
            \Directus\Authentication\User\Provider\UserTableGatewayProvider::class,
            ['findWhere'],
            [$tableGateway]
        );

        return $userProvider;
    }

    protected function addEmptyUserToMockUserProvider(PHPUnit_Framework_MockObject_MockObject &$mock)
    {
        $mock
            ->expects($this->any())
            ->method('findWhere')
            ->will($this->returnValue(new \Directus\Authentication\User\User()));
    }

    protected function addNotFoundUserToMockUserProvider(PHPUnit_Framework_MockObject_MockObject &$mock)
    {
        $mock
            ->expects($this->atMost(1))
            ->method('findWhere')
            ->with(
                ['invite_token' => 'invalid-token']
            )
            ->will($this->returnValue(null));
    }

    /**
     * @param $adapter
     * @param int|null $type User type (valid, inactive)
     *
     * @return \Directus\Database\TableGateway\DirectusUsersTableGateway
     */
    protected function getTableGateway($adapter, $type = null)
    {
        $data = $this->getData($type);

        $mock = create_mock($this, 'Directus\Database\TableGateway\DirectusUsersTableGateway', ['selectWith'], [$adapter]);

        $row = new \Directus\Database\RowGateway\BaseRowGateway('id', 'directus_users', $adapter);
        $row->populate($data, true);

        $resultSetMock = create_mock($this, 'Zend\Db\ResultSet\ResultSet', ['current']);
        $resultSetMock->expects($this->any())->method('current')->will($this->returnValue($row));
        $mock->expects($this->any())->method('selectWith')->will($this->returnValue($resultSetMock));

        return $mock;
    }

    protected function getData($type = null)
    {
        $data = [
            'id' => 1,
            'email' => 'admin@getdirectus.com',
            'password' => password_hash('secret-password', PASSWORD_DEFAULT, ['cost' => 12]),
            'group' => 1,
            'invite_token' => 'invitation-token'
        ];

        if ($type !== static::DATA_USER_INACTIVE) {
            $data['status'] = 1;
        }

        return $data;
    }
}
