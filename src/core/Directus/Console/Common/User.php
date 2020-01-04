<?php

namespace Directus\Console\Common;

use Directus\Console\Common\Exception\PasswordChangeException;
use Directus\Console\Common\Exception\UserUpdateException;
use function Directus\get_directus_setting;
use Directus\Util\Installation\InstallerUtils;
use Zend\Db\TableGateway\TableGateway;

class User
{
    private $directus_path;
    private $app;
    private $db;
    private $usersTableGateway;

    public function __construct($base_path, $projectName = null)
    {
        if (null == $base_path) {
            $base_path = \Directus\base_path();
        }

        $this->directus_path = $base_path;
        $this->app = InstallerUtils::createApp($base_path, $projectName);
        $this->db = $this->app->getContainer()->get('database');

        $this->usersTableGateway = new TableGateway('directus_users', $this->db);
    }

    /**
     * Check the existance of user in the system.
     *
     * The function will check the user of given their e-mail address exist in
     * the system or not.
     *
     * @param mixed $email
     */
    public function userExists($email)
    {
        try {
            $rowset = $this->usersTableGateway->select([
                'email' => $email,
            ]);
            if ($rowset->count() > 0) {
                return true;
            }

            return false;
        } catch (\PDOException $ex) {
            return false;
        }
    }

    /**
     *  Change the password of a user given their e-mail address.
     *
     *  The function will change the password of a user given their e-mail
     *  address. If there are multiple users with the same e-mail address, and
     *  this should never be the case, all of their passwords would be changed.
     *
     *  The function will generate a new salt for every password change.
     *
     * @param string $email    the e-mail of the user whose password is being
     *                         changed
     * @param string $password the new password
     *
     * @throws PasswordChangeException thrown when password change has failed
     */
    public function changePassword($email, $password)
    {
        $auth = $this->app->getContainer()->get('auth');

        $passwordValidation = get_directus_setting('password_policy');
        if (!empty($passwordValidation)) {
            if (!preg_match($passwordValidation, $password, $match)) {
                throw new PasswordChangeException('Password is not valid.');
            }
        }

        $hash = $auth->hashPassword($password);
        $user = $this->usersTableGateway->select(['email' => $email])->current();

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        try {
            $update = [
                'password' => $hash,
            ];

            $changed = $this->usersTableGateway->update($update, ['email' => $email]);
            if (0 == $changed) {
                throw new PasswordChangeException('Could not change password for '.$email.': '.'e-mail not found.');
            }
        } catch (\PDOException $ex) {
            throw new PasswordChangeException('Failed to change password'.': '.str($ex));
        }
    }

    /**
     *  Change the e-mail of a user given their ID.
     *
     *  The function will change the e-mail of a user given their ID. This may have
     *  undesired effects, this function is mainly useful during the setup/install
     *  phase.
     *
     * @param string $id    the ID of the user whose e-mail address we want to change
     * @param string $email the new e-mail address for the use
     *
     * @throws UserUpdateException thrown when the e-mail address change fails
     */
    public function changeEmail($id, $email)
    {
        $update = [
            'email' => $email,
        ];

        try {
            $this->usersTableGateway->update($update, ['id' => $id]);
        } catch (\PDOException $ex) {
            throw new PasswordChangeException('Could not change email for ID '.$id.': '.str($ex));
        }
    }
}
