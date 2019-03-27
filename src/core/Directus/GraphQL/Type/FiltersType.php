<?php
namespace Directus\GraphQL\Type;

use Directus\GraphQL\Types;
use Directus\GraphQL\FieldsConfig;
use GraphQL\Type\Definition\InputObjectType;

class FiltersType extends InputObjectType
{
    public function __construct($inputFromQuery = null)
    {
        $fieldConfig = new FieldsConfig($inputFromQuery);
        $config =  [
            'name' => $inputFromQuery . 'Filter',
            'fields' =>  $fieldConfig->getFilters()
        ];
        parent::__construct($config);
    }
}
