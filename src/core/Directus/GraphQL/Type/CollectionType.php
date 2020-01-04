<?php

namespace Directus\GraphQL\Type;

use Directus\GraphQL\Types;
use Directus\Util\StringUtils;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class CollectionType extends ObjectType
{
    public function __construct($type)
    {
        $config = [
            'name' => StringUtils::toPascalCase(substr_replace($type, '', -4)), //Remove the 'item' word from type.
            'fields' => [
                'data' => Types::listOf($type),
                'meta' => Types::meta(),
            ],
            'interfaces' => [
                Types::node(),
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
                $method = 'resolve'.ucfirst($info->fieldName);
                if (method_exists($this, $method)) {
                    return $this->{$method}($value, $args, $context, $info);
                }

                return $value[$info->fieldName];
            },
        ];
        parent::__construct($config);
    }
}
