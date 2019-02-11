<?php

namespace Directus\Services;

use Directus\Application\Application;
use Directus\Util\StringUtils;


class GraphQLService extends AbstractService
{
    /**
     * @param array $types
     *
     * @return array
     */
    public function generateSchema(array $types)
    {

        //TODO: Use the config from api.php
        $graphQLFile = 'GraphQL/_/schema.graphql';

        $schemaFile = fopen($graphQLFile, "w");

        $queryTxt = "type Query {\n";
        foreach($types as $type){
            $typeTxt = "type ".StringUtils::underscoreToCamelCase(strtolower($type['collection']), true) ." { \n";
            foreach($type['fields'] as $fieldName => $fieldType){
                $typeTxt .= "\t".$fieldName.": ".$fieldType."\n";
            }
            $typeTxt .="}\n";
            fwrite($schemaFile, $typeTxt);

            $queryTxt .= "\t".strtolower($type['collection']).": [".StringUtils::underscoreToCamelCase(strtolower($type['collection']), true)."!]!\n";
            $queryTxt .= "\t".strtolower($type['collection'])."Item: ".StringUtils::underscoreToCamelCase(strtolower($type['collection']), true)."\n";
        }
        $queryTxt .= "}\n";
        fwrite($schemaFile, $queryTxt);
        fclose($schemaFile);

        return ['schemaPath' => $graphQLFile];
    }
}
