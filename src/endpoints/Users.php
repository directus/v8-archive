<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Authentication\Provider;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableGatewayFactory;
use Directus\Permissions\Acl;
use Directus\Util\DateUtils;
use Directus\Util\StringUtils;

class Users extends Route
{
    /** @var $usersGateway DirectusUsersTableGateway */
    protected $usersGateway;

    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('', [$this, 'all']);
        $app->post('', [$this, 'create']);
        $app->get('/{id}', [$this, 'one']);
        $app->post('/invite', [$this, 'invite']);
        $app->map(['PUT', 'PATCH'], '/{id}', [$this, 'update']);
        $app->delete('/{id}', [$this, 'delete']); // move separated method
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $responseData = $this->findUsers($params);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $usersGateway = $this->getTableGateway();
        $payload = $request->getParsedBody();
        $email = $request->getParsedBodyParam('email');

        $this->validateRequest($request, $this->createConstraintFor('directus_users'));

        $user = $usersGateway->findOneBy('email', $email);
        if ($user) {
            $payload['id'] = $user['id'];
            $payload['status'] = $usersGateway::STATUS_ACTIVE;
        }

        $user = $usersGateway->updateRecord($payload);

        $responseData = $this->findUsers(['id' => $user['id'], 'status' => false]);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function one(Request $request, Response $response)
    {
        $params = $request->getQueryParams();
        $responseData = $this->findUsers(array_merge($params, [
            'id' => $this->getUserId($request->getAttribute('id')),
            'status' => false
        ]));

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function invite(Request $request, Response $response)
    {
        $email = $request->getParsedBodyParam('email');
        $emails = explode(',', $email);

        foreach ($emails as $email) {
            $data = ['email' => $email];
            $this->validate($data, ['email' => 'required|email']);
        }

        foreach ($emails as $email) {
            $this->sendInvitationTo($email);
        }

        $responseData = $this->findUsers([
            'filters' => [
                'email' => ['in' => $emails]
            ]
        ]);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $id = $this->getUserId($request->getAttribute('id'));
        $usersGateway = $this->getTableGateway();
        $payload = $request->getParsedBody();

        switch ($request->getMethod()) {
            case 'DELETE':
                $usersGateway->delete(['id' => $id]);
                return $this->responseWithData($request, $response, []);
                break;
            case 'PATCH':
            case 'PUT':
                $this->validateRequestWithTable($request, 'directus_users');
                $columnsToValidate = [];
                if ($request->isPatch()) {
                    $columnsToValidate = array_keys($payload);
                }
                $this->createConstraintFor('directus_users', $columnsToValidate);
                $payload['id'] = $id;
                break;
        }

        $user = $usersGateway->updateRecord($payload);

        $responseData = $this->findUsers(['id' => $user['id'], 'status' => false]);

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        return $this->update($request, $response);
    }

    /**
     * @param string $email
     */
    protected function sendInvitationTo($email)
    {
        // TODO: Builder/Service to get table gateway
        // $usersRepository = $repositoryCollection->get('users');
        // $usersRepository->add();
        $dbConnection = $this->container->get('database');
        $acl = $this->container->get('acl');
        /** @var Provider $auth */
        $auth = $this->container->get('auth');
        $tableGateway = new DirectusUsersTableGateway($dbConnection, $acl);

        $invitationToken = StringUtils::randomString(128);

        $user = $tableGateway->findOneBy('email', $email);

        // TODO: Throw exception when email exists
        // Probably resend if the email exists?
        if (!$user) {
            $result = $tableGateway->insert([
                'status' => DirectusUsersTableGateway::STATUS_DISABLED,
                'email' => $email,
                'token' => StringUtils::randomString(32),
                'invite_token' => $invitationToken,
                'invite_date' => DateUtils::now(),
                'invite_sender' => $auth->getUserAttributes('id'),
                'invite_accepted' => 0
            ]);

            if ($result) {
                send_user_invitation_email($email, $invitationToken);
            }
        }
    }

    /**
     * Replace "me" with the authenticated user
     *
     * @param null $id
     *
     * @return int|null
     */
    public function getUserId($id = null)
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
    public function findUsers(array $params = [])
    {
        return $this->getEntriesAndSetResponseCacheTags($this->getTableGateway(), $params);
    }

    /**
     * Gets the user table gateway
     *
     * @return DirectusUsersTableGateway
     */
    protected function getTableGateway()
    {
        if (!$this->usersGateway) {
            $this->usersGateway = TableGatewayFactory::create('directus_users', [
                'container' => $this->container,
                'acl' => $this->container->get('acl'),
                'adapter' => $this->container->geT('database')
            ]);
        }

        return $this->usersGateway;
    }
}
