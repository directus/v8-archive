<?php
namespace Directus\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Directus\GraphQL\Type\DirectuFilesType;
use Directus\GraphQL\Types;
use Directus\Services\FilesServices;
use Directus\Application\Container;
use Directus\Application\Application;


class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => [
                'directusFile' => [
                    'type' => Types::directusFile(),
                    'description' => 'Return single file item.',
                    'args' => [
                        'id' => Type::nonNull(Types::id()),
                    ],
                    'resolve' => function($val, $args, $context, ResolveInfo $info) {


                        // $data = [
                        //         '1' => ['id' => 1 , 'filename' => 'Hem 1'],
                        //         '2' => ['id' => 2 , 'filename' => 'Hem 2'],
                        //         '3' => ['id' => 3 , 'filename' => 'Hem 3'],
                        // ];

                        // return $data[$args['id']];
                        $container = Application::getInstance()->getContainer();
                        $service = new FilesServices($container);
                        return $service->findByIds(
                            $args['id']
                        )['data'];


                    }
                ],
                'hello' => Type::string()
                ],
                'resolveField' => function($val, $args, $context, ResolveInfo $info) {
                    return $this->{$info->fieldName}($val, $args, $context, $info);
                }
        ];
        parent::__construct($config);

    }

    public function hello()
    {
        return 'Your graphql-php endpoint is ready! Use GraphiQL to browse API';
    }

}