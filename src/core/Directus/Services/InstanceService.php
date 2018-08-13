<?php

namespace Directus\Services;

use Directus\Exception\ForbiddenException;
use Directus\Exception\UnauthorizedException;
use function Directus\get_max_upload_size;
use function Directus\get_server_timeout;
use function Directus\thumbnail_get_supported;
use Directus\Util\ArrayUtils;
use Directus\Util\Installation\InstallerUtils;

class InstanceService extends AbstractService
{
    public function findAllInfo()
    {
        // TODO: Move Admin verification to middleware
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Only Admin can see this information');
        }

        return [
            'data' => [
                'api' => [
                    'thumbnail_supported' => thumbnail_get_supported()
                ],
                'server' => [
                    'timeout' => get_server_timeout(),
                    'max_upload_size' => get_max_upload_size()
                ]
            ]
        ];
    }

    public function create(array $data)
    {
        if ($this->isLocked()) {
            throw new ForbiddenException('Creating new instance is locked');
        }

        $data = ArrayUtils::defaults(['user_token' => null], $data);

        $this->validate($data, [
            'env' => 'string',

            'force' => 'bool',

            'db_host' => 'string',
            'db_port' => 'numeric',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_password' => 'string',

            'mail_from' => 'string',
            'cors_enabled' => 'bool',

            'project_name' => 'string',
            'user_email' => 'required|email',
            'user_password' => 'required|string',
            'user_token' => 'string'
        ]);

        $force = ArrayUtils::pull($data, 'force', false);
        $env = ArrayUtils::get($data, 'env', '_');
        $basePath = $this->container->get('path_base');

        InstallerUtils::ensureCanCreateConfig($basePath, $data, $force);
        InstallerUtils::ensureCanCreateTables($basePath, $data, $force);

        InstallerUtils::createConfig($basePath, $data, $force);
        InstallerUtils::createTables($basePath, $env, $force);
        InstallerUtils::addDefaultSettings($basePath, $data, $env);
        InstallerUtils::addDefaultUser($basePath, $data, $env);
    }

    /**
     * Checks whether .lock file exists
     *
     * @return bool
     */
    protected function isLocked()
    {
        $basePath = $this->container->get('path_base');
        $lockFilePath = $basePath . '/.lock';

        return file_exists($lockFilePath) && is_file($lockFilePath);
    }
}
