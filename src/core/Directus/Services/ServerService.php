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
            'data' => [
                'api' => [
                    'version' => Application::DIRECTUS_VERSION
                ],
                'server' => [
                    'general' => [
                        'php_version' => phpversion(),
                        'php_api' => php_sapi_name()
                    ],
                    'max_upload_size' => \Directus\get_max_upload_size()
                ]
            ]
        ];
    }
}
