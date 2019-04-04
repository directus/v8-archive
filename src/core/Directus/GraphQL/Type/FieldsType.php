<?php
namespace Directus\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;
use Directus\GraphQL\FieldsConfig;

class FieldsType extends ObjectType
{
    public function __construct($inputFromQuery = null)
    {
        $fieldConfig = new FieldsConfig($inputFromQuery);
        $config = [
            'name' => $inputFromQuery,
            'description' => 'Collection Fields.',
            'fields' => function () use ($fieldConfig) {
                return $fieldConfig->getFields();
            },
            'interfaces' => [
                Types::node()
            ],
            'resolveField' => function ($value, $args, $context, ResolveInfo $info) {
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
