<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Container;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableGatewayFactory;
use Directus\Exception\Http\BadRequestException;
use Directus\Permissions\Acl;
use Directus\Util\DateUtils;
use Directus\Util\StringUtils;
use Directus\Util\Validator;

class Users extends Route
{
    /** @var $usersGateway DirectusUsersTableGateway */
    protected $usersGateway;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->usersGateway = TableGatewayFactory::create('directus_users', [
            'acl' => $this->container->get('acl'),
            'adapter' => $this->container->geT('database')
        ]);
    }

    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id)}', [$this, 'one']);
        $app->post('/invite', [$this, 'invite']);
        $app->map(['DELETE', 'PUT', 'PATCH'], '/{id}', [$this, 'update']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function all(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $data = $this->findUsers($params);

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function create(Request $request, Response $response)
    {
        $usersGateway = $this->usersGateway;
        $payload = $request->getParsedBody();
        $email = $request->getParsedBodyParam('email');

        $this->validateEmailOrFail($email);

        $user = $usersGateway->findOneBy('email', $email);
        if ($user) {
            $payload['id'] = $user['id'];
            $payload['status'] = $usersGateway::STATUS_ACTIVE;
        }

        if (!empty($requestPayload['email'])) {
            $avatar = DirectusUsersTableGateway::get_avatar($payload['email']);
            $payload['avatar'] = $avatar;
        }

        $user = $usersGateway->updateRecord($requestPayload);

        $data = $this->findUsers(['id' => $user['id']]);

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function one(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $params['id'] = $this->getUserId($request->getAttribute('id'));

        $data = $this->findUsers($params);

        return $this->withData($response, $data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function invite(Request $request, Response $response)
    {
        $email = $request->getParsedBodyParam('email');
        $emails = explode(',', $email);

        foreach ($emails as $email) {
            $this->sendInvitationTo($email);
        }

        return $this->withData($response, []);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    protected function update(Request $request, Response $response)
    {
        $id = $this->getUserId($request->getAttribute('id'));
        $usersGateway = $this->usersGateway;
        $payload = $request->getParsedBody();
        $email = $request->getParsedBodyParam('email');

        if ($email) {
            $this->validateEmailOrFail($email);
        }

        switch ($request->getMethod()) {
            case 'DELETE':
                $payload = [];
                $payload['id'] = $id;
                $payload['status'] = $usersGateway::STATUS_HIDDEN;
                break;
            case 'PATCH':
            case 'PUT':
                $payload['id'] = $id;
                break;
        }

        if (!empty($email)) {
            $avatar = DirectusUsersTableGateway::get_avatar($email);
            $requestPayload['avatar'] = $avatar;
        }

        $user = $usersGateway->updateRecord($payload);

        $data = $this->findUsers(['id' => $user['id']]);

        return $this->withData($response, $data);
    }

    /**
     * @param string $email
     */
    protected function sendInvitationTo($email)
    {
        $this->validateEmailOrFail($email);
        // @TODO: Builder/Service to get table gateway
        // $usersRepository = $repositoryCollection->get('users');
        // $usersRepository->add();
        $ZendDb = $this->container->get('zenddb');
        $acl = $this->container->get('acl');
        $auth = $this->container->get('auth');
        $tableGateway = new DirectusUsersTableGateway($ZendDb, $acl);

        $token = StringUtils::randomString(128);
        $result = $tableGateway->insert([
            'status' => STATUS_DRAFT_NUM,
            'email' => $email,
            'token' => StringUtils::randomString(32),
            'invite_token' => $token,
            'invite_date' => DateUtils::now(),
            'invite_sender' => $auth->getUserInfo('id'),
            'invite_accepted' => 0
        ]);

        if ($result) {
            send_user_invitation_email($email, $token);
        }
    }

    /**
     * Replace "me" with the authenticated user
     *
     * @param null $id
     *
     * @return int|null
     */
    protected function getUserId($id = null)
    {
        if ($id === 'me') {
            /** @var Acl $acl */
            $acl = $this->container->get('acl');
            $id = $acl->getUserId();
        }

        return $id;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function findUsers(array $params = [])
    {
        return $this->getEntriesAndSetResponseCacheTags($this->usersGateway, $params);
    }

    /**
     * @param $email
     *
     * @throws BadRequestException
     */
    protected function validateEmailOrFail($email)
    {
        if (!Validator::email($email)) {
            throw new BadRequestException(__t('invalid_email'));
        }
    }
}
