<?php

namespace Directus\Application;

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Database\SchemaManager;
use Directus\Database\TableGateway\RelationalTableGateway;
use Directus\Exception\BadRequestException;
use Directus\Hook\Emitter;
use Directus\Hook\Payload;
use Directus\Util\ArrayUtils;
use Directus\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class Route
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->validator = new Validator();
    }

    /**
     * @param Request $request
     * @param string $tableName
     *
     * @throws BadRequestException
     */
    public function validateRequestWithTable(Request $request, $tableName)
    {
        $payload = $request->getParsedBody();

        $this->validateDataWithTable($request, $payload, $tableName);
    }

    public function validateDataWithTable(Request $request, array $payload, $tableName)
    {
        // Only validate create/update
        if (in_array($request->getMethod(), ['PATCH', 'PUT', 'POST'])) {
            $columnsToValidate = [];

            // TODO: Validate empty request
            // If the user PATCH, POST or PUT with empty body, must throw an exception to avoid continue the execution
            // with the exception of POST, that can use the default value instead
            // TODO: Crate a email interface for the sake of validation
            if ($request->isPatch()) {
                $columnsToValidate = array_keys($payload);

                if (empty($columnsToValidate)) {
                    return;
                }
            }

            $this->validate($payload, $this->createConstraintFor($tableName, $columnsToValidate));
        }
    }

    /**
     * @param Request $request
     * @param array $constraints
     *
     * @return array
     *
     * @throws BadRequestException
     */
    public function validateRequest(Request $request, array $constraints)
    {
        $this->validate($request->getParsedBody(), $constraints);
    }

    /**
     * @param array $data
     * @param array $constraints
     *
     * @throws BadRequestException
     */
    public function validate(array $data, array $constraints)
    {
        $constraintViolations = $this->getViolations($data, $constraints);

        $this->throwErrorIfAny($constraintViolations);
    }

    /**
     * @param array $data
     * @param array $constraints
     *
     * @return array
     */
    protected function getViolations(array $data, array $constraints)
    {
        $violations = [];

        foreach ($constraints as $field => $constraint) {
            if (is_string($constraint)) {
                $constraint = explode('|', $constraint);
            }

            $violations[$field] = $this->validator->validate(ArrayUtils::get($data, $field), $constraint);
        }

        return $violations;
    }

    /**
     * Throws an exception if any violations was made
     *
     * @param ConstraintViolationList[] $violations
     *
     * @throws BadRequestException
     */
    protected function throwErrorIfAny(array $violations)
    {
        $results = [];

        /** @var ConstraintViolationList $violation */
        foreach ($violations as $field => $violation) {
            $iterator = $violation->getIterator();

            $errors = [];
            while ($iterator->valid()) {
                $constraintViolation = $iterator->current();
                $errors[] = $constraintViolation->getMessage();
                $iterator->next();
            }

            if ($errors) {
                $results[] = sprintf('%s: %s', $field, implode(', ', $errors));
            }
        }

        if (count($results) > 0) {
            throw new BadRequestException(implode(' ', $results));
        }
    }

    /**
     * Creates the constraint for a an specific table columns
     *
     * @param string $tableName
     * @param array $columns List of columns name
     *
     * @return array
     */
    protected function createConstraintFor($tableName, array $columns = [])
    {
        /** @var SchemaManager $schemaManager */
        $schemaManager = $this->container->get('schema_manager');
        $tableObject = $schemaManager->getTableSchema($tableName);

        $constraints = [];

        if ($columns === null) {
            return $constraints;
        }

        foreach ($tableObject->getColumns($columns) as $column) {
            $columnConstraints = [];

            if ($column->hasAutoIncrement()) {
                continue;
            }

            if ($column->isRequired() || (!$column->isNullable() && $column->getDefaultValue() == null)) {
                $columnConstraints[] = 'required';
            }

            if (!empty($columnConstraints)) {
                $constraints[$column->getName()] = $columnConstraints;
            }
        }

        return $constraints;
    }

    /**
     * Convert array data into an API output format
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @param array $options
     *
     * @return Response
     */
    public function responseWithData(Request $request, Response $response, array $data, array $options = [])
    {
        $data = $this->triggerResponseFilter($request, $data, (array) $options);

        // TODO: Response will support xml

        return $response->withJson($data);
    }

    /**
     * Trigger a response filter
     *
     * @param Request $request
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    protected function triggerResponseFilter(Request $request, array $data, array $options = [])
    {
        $meta = ArrayUtils::get($data, 'meta');
        $method = $request->getMethod();

        $attributes = [
            'meta' => $meta,
            'request' => [
                'path' => $request->getUri()->getPath(),
                'method' => $method
            ]
        ];

        /** @var Emitter $emitter */
        $emitter = $this->container->get('hook_emitter');

        $payload = $emitter->apply('response', $data, $attributes);
        $payload = $emitter->apply('response.' . $method, $payload->getData());

        if ($meta['table']) {
            $emitter->apply('response.' . $meta['table'], $payload->getData());
            $payload = $emitter->apply(sprintf('response.%s.%s',
                $meta['table'],
                $method
            ), $payload->getData());
        }

        return $payload->getData();
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
        $hookEmitter->removeListenerWithIndex($listenerId);

        return $result;
    }

}
