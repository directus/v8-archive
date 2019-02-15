<?php
namespace Directus\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;

class FileThumbnailType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Directus file thumbnail',
            'description' => 'Directus thumbnail of the file.',
            'fields' => [
                'url' => Types::string(),
                'relative_url' => Types::string(),
                'dimension' => Types::string(),
                'width' => Types::int(),
                'height' => Types::int(),
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