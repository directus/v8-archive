<?php

namespace Directus\Services;

use GraphQL\Utils\BuildSchema;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Error\Debug;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Directus\GraphQL\Types;


class GraphQLService extends AbstractService
{
    public function index($inputs)
    {
        $schema = new Schema([
            'query' => Types::query()
        ]);

        $inputs = json_decode($inputs, true);
        $query = $inputs['query'];
        $variableValues = isset($inputs['variables']) ? $inputs['variables'] : null;

        try {
            $debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS;
            $rootValue = null;
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $responseData = $result->toArray($debug);
        } catch (\Exception $e) {
            $responseData = [
                'errors' => [
                    [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
        return $responseData;
    }
}
