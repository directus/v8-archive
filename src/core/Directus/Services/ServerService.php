<?php

namespace Directus\Services;

use Directus\Application\Application;
use Directus\Exception\UnauthorizedException;

class ServerService extends AbstractService
{
    public function findAllInfo()
    {
        // TODO: Move Admin verification to middleware
        if (!$this->getAcl()->isAdmin()) {
            throw new UnauthorizedException('Only Admin can see this information');
        }

        return [
            'api' => [
                'version' => Application::DIRECTUS_VERSION
            ],
            'server' => [
                'general' => [
                    'php_version' => phpversion(),
                    'php_api' => php_sapi_name()
                ],
                'core' => [
                    array_map(function ($v) {
                        return ['current' => $v];
                    }, ini_get_all('core', false))
                ]
            ]
        ];
    }
}
