<?php

namespace Directus\Services;

use Directus\Application\Container;
use Directus\Database\Schema\SchemaManager;
use Directus\Database\TableGateway\DirectusUsersTableGateway;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Exception\ForbiddenException;
use Directus\Util\DateUtils;
use Directus\Util\StringUtils;

class UsersService extends AbstractService
{
    /**
     * @var string
     */
    protected $tableGateway;

    /**
     * @var string
     */
    protected $collection;

    /**
     * @var ItemsService
     */
    protected $itemsService;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->collection = SchemaManager::TABLE_USERS;
        $this->itemsService = new ItemsService($this->container);
    }

    public function create(array $data, array $params = [])
    {
        return $this->itemsService->createItem($this->collection, $data, $params);
    }

    public function update($id, array $data, array $params = [])
    {
        return $this->itemsService->update(
            $this->collection,
            $this->getUserId($id),
            $data,
            $params
        );
    }

    public function find($id, array $params = [])
    {
        return $this->itemsService->find(
            $this->collection,
            $this->getUserId($id),
            $params
        );
    }

    public function delete($id, array $params = [])
    {
        return $this->itemsService->delete(
            $this->collection,
            $this->getUserId($id),
            $params
        );
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function findAll(array $params = [])
    {
        return $this->getItemsAndSetResponseCacheTags($this->getTableGateway(), $params);
    }

    public function invite(array $emails, array $params = [])
    {
        if (!$this->getAcl()->isAdmin()) {
            throw new ForbiddenException('Inviting user was denied');
        }

        foreach ($emails as $email) {
            $data = ['email' => $email];
            $this->validate($data, ['email' => 'required|email']);
        }

        foreach ($emails as $email) {
            $this->sendInvitationTo($email);
        }

        return $this->findAll([
            'status' => false,
            'filters' => [
                'email' => ['in' => $emails]
            ]
        ]);
    }

    /**
     * @param string $email
     */
    protected function sendInvitationTo($email)
    {
        // TODO: Builder/Service to get table gateway
        // $usersRepository = $repositoryCollection->get('users');
        // $usersRepository->add();
        $tableGateway = $this->createTableGateway($this->collection);
        $invitationToken = StringUtils::randomString(128);
        $user = $tableGateway->findOneBy('email', $email);

        // TODO: Throw exception when email exists
        // Probably resend if the email exists?
        // TODO: Add activity
        if (!$user) {
            $result = $tableGateway->insert([
                'status' => DirectusUsersTableGateway::STATUS_DISABLED,
                'email' => $email,
                'token' => StringUtils::randomString(32),
                'invite_token' => $invitationToken,
                'invite_date' => DateUtils::now(),
                'invite_sender' => $this->getAcl()->getUserId(),
                'invite_accepted' => 0
            ]);

            if ($result) {
                // TODO: This should be a moved to a hook
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
    protected function getUserId($id = null)
    {
        if ($id === 'me') {
            $id = $this->getAcl()->getUserId();
        }

        return $id;
    }

    /**
     * Gets the user table gateway
     *
     * @return RelationalTableGateway
     */
    protected function getTableGateway()
    {
        if (!$this->tableGateway) {
            $this->tableGateway = $this->createTableGateway($this->collection);
        }

        return $this->tableGateway;
    }
}
