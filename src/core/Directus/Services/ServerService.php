<?php

namespace Directus\Services;

use Directus\Application\Application;
use Directus\Exception\UnauthorizedException;
use function Directus\get_project_info;

class ServerService extends AbstractService
{
    /**
     * @param bool $global
     * @return array
     *
     * @throws UnauthorizedException
     */
    public function findAllInfo($global = true)
    {
        $data = [
            'api' => [
                'version' => Application::DIRECTUS_VERSION
            ],
            'server' => [
                'max_upload_size' => \Directus\get_max_upload_size($global)
            ]
        ];

        if ($global !== true) {
            $config = $this->getContainer()->get('config');
            $data['api']['database'] = $config->get('database.type');
            $data['api'] = array_merge($data['api'], $this->getPublicInfo());
        }

        if ($this->getAcl()->isAdmin()) {
            $data['server']['general'] = [
                'php_version' => phpversion(),
                'php_api' => php_sapi_name()
            ];
        }

        return [
            'data' => $data
        ];
    }

    /**
     * Return Project public data
     *
     * @return array
     */
    public function getPublicInfo()
    {
        return get_project_info();
    }
}
