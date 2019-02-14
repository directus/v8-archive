<?php
namespace Directus\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Directus\GraphQL\Types;
use GraphQL\Type\Definition\ResolveInfo;

class DirectusFileType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'DirectusFile',
            'description' => 'Directus File.',
            'fields' => [
                'id' => Types::id(),
                'filename' => Types::string(),
                'title' => Types::string(),
                'description' => Types::string(),
                'location' => Types::string(),
                'width' => Types::int(),
                'height' => Types::int(),
                'filesize' => Types::int(),
                'duration' => Types::string(),
                'metadata' => Types::string(),
                'type' => Types::string(),
                'charset' => Types::string(),
                'storage' => Types::string(),
                'full_url' => Types::string(),
                'url' => Types::string(),
                'thumbnails' => Types::string()
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

    public function resolveFull_url($value)
    {
        return $value['data']['full_url'];
    }

    public function resolveUrl($value)
    {
        return $value['data']['url'];
    }



}