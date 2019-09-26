<?php

namespace Directus\Services;

use Directus\Application\Application;
use Directus\Exception\UnauthorizedException;
use function Directus\get_project_info;

class ServerService extends AbstractService
{
    // Uses php.ini to get configuration values
    const INFO_SETTINGS_CORE = 1;
    // Uses runtime configuration instead of php.ini values
    const INFO_SETTINGS_RUNTIME = 2;

    /**
     * @param bool $global
     * @param int|null $configuration
     *
     * @return array
     *
     * @throws UnauthorizedException
     */
    public function findAllInfo($global = true, $configuration = null)
    {
        if ($configuration === null) {
            $configuration = self::INFO_SETTINGS_RUNTIME;
        }

        $data = [
            'api' => [
                'version' => Application::DIRECTUS_VERSION
            ],
            'server' => [
                'max_upload_size' => \Directus\get_max_upload_size($configuration === self::INFO_SETTINGS_CORE),
            ]
        ];

        if ($global !== true) {
            $data['api'] = array_merge($data['api'], $this->getPublicInfo());
            $data['api']['database'] =$this->getDatabaseType();            
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

    /**
     * Return database implementation name
     * 
     * @return String
     */
    public function getDatabaseType()
    {
        $config = $this->getContainer()->get('config');
        return $config->get('database.type');
    }
}
