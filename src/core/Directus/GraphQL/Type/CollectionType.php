<?php
namespace Directus\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;

class CollectionType extends ObjectType
{
    public function __construct($type)
    {
        $config = [
            'name' => 'Collection of '.$type,
            'description' => 'Collection with data and meta',
            'fields' => [
              'data' => Types::listOf($type),
              'meta' => Types::meta()
            ],
            'interfaces' => [
                Types::node()
            ],
            'resolveField' => function($value, $args, $context, ResolveInfo $info) {
                $method = 'resolve' . ucfirst($info->fieldName);
                if (method_exists($this, $method)) {
                    return $this->{$method}($value, $args, $context, $info);
                } else {
                    return $value[$info->fieldName];
                }
            }
        ];
        parent::__construct($config);
    }
}