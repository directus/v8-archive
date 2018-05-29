<?php

namespace Directus\Application\Http\Middlewares;

use Directus\Application\Container;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Database\SchemaService;
use Directus\Database\TableGateway\BaseTableGateway;
use Directus\Database\TableGatewayFactory;

class TableGatewayMiddleware extends AbstractMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $container = $this->container;

        // tablegateway dependency
        SchemaService::setAclInstance($container->get('acl'));
        SchemaService::setConnectionInstance($container->get('database'));
        SchemaService::setConfig($container->get('config'));
        BaseTableGateway::setHookEmitter($container->get('hook_emitter'));
        BaseTableGateway::setContainer($container);
        TableGatewayFactory::setContainer($container);

        $container['app.settings'] = function (Container $container) {
            $dbConnection = $container->get('database');
            $DirectusSettingsTableGateway = new \Zend\Db\TableGateway\TableGateway('directus_settings', $dbConnection);
            $rowSet = $DirectusSettingsTableGateway->select();

            $settings = [];
            foreach ($rowSet as $setting) {
                $settings[$setting['scope']][$setting['key']] = $setting['value'];
            }

            return $settings;
        };

        return $next($request, $response);
    }
}
