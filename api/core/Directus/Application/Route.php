<?php

namespace Directus\Application;

use Directus\Application\Http\Response;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Hook\Emitter;
use Directus\Hook\Payload;

abstract class Route
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Parse the output data
     *
     * @param Response $response
     * @param array $data
     * @param array $options
     *
     * @return Response
     */
    public function withData(Response $response, array $data, array $options = [])
    {
        // TODO: Event parsing output
        // This event can guess/change the output from json to xml

        return $response->withJson($data);
    }

    protected function tagResponseCache($tags)
    {
        $this->container->get('response_cache')->tag($tags);
    }

    protected function invalidateCacheTags($tags)
    {
        $this->container->get('cache')->getPool()->invalidateTags($tags);
    }

    /**
     * @param RelationalTableGateway $gateway
     * @param array $params
     * @param \Closure|null $queryCallback
     * @return array|mixed
     */
    protected function getEntriesAndSetResponseCacheTags(RelationalTableGateway $gateway, array $params, \Closure $queryCallback = null)
    {
        return $this->getDataAndSetResponseCacheTags([$gateway, 'getEntries'], [$params, $queryCallback]);
    }

    /**
     * @param callable $callable
     * @param array $callableParams
     * @param null $pkName
     * @return array|mixed
     */
    protected function getDataAndSetResponseCacheTags(Callable $callable, array $callableParams = [], $pkName = null)
    {
        $container = $this->container;

        if(is_array($callable) && $callable[0] instanceof RelationalTableGateway) {
            /** @var $callable[0] RelationalTableGateway */
            $pkName = $callable[0]->primaryKeyFieldName;
        }

        $setIdTags = function(Payload $payload) use($pkName, $container) {
            $tableName = $payload->attribute('tableName');

            $this->tagResponseCache('table_'.$tableName);
            $this->tagResponseCache('privilege_table_'.$tableName.'_group_'.$container->get('acl')->getGroupId());

            foreach($payload->getData() as $item) {
                $this->tagResponseCache('entity_'.$tableName.'_'.$item[$pkName]);
            }

            return $payload;
        };

        /** @var Emitter $hookEmitter */
        $hookEmitter = $container->get('hook_emitter');

        $listenerId = $hookEmitter->addFilter('table.select', $setIdTags, Emitter::P_LOW);
        $result = call_user_func_array($callable, $callableParams);
        $hookEmitter->removeListener($listenerId);

        return $result;
    }

}
