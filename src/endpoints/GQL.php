<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\TablesService;
use Directus\Services\GraphQLService;
use GraphQL\Utils\BuildSchema;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Directus\GraphQL\CustomResolver;
use GraphQL\Type\Definition\ResolveInfo;

class GQL extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('/', [$this, 'index']); // Used for to access the graphql schema

        /* Create a schema.graphql file. This will be a post request, but for the ease of testing
        *  we are using get request.
        */
        $app->get('/generateSchema', [$this, 'generateSchema']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response)
    {

        $contents = file_get_contents('GraphQL/_/schema.graphql');
        $schema = BuildSchema::build($contents);

        $rawInput = $request->getBody();
        $input = json_decode($rawInput, true);
        $query = $input['query'];
        $variableValues = isset($input['variables']) ? $input['variables'] : null;

        $this->container->get('logger')->info($source);

        try {
            $customResolver = new CustomResolver();
            $rootValue = [];
            $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues, null, $customResolver($source, $args, $context, $info), null);
            $responseData = $result->toArray();
        } catch (\Exception $e) {
            $responseData = [
                'errors' => [
                    [
                        'message' => $e->getMessage()
                    ]
                ]
            ];
        }
        //header('Content-Type: application/json');
        return $this->responseWithData($request, $response, $responseData);

    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function generateSchema(Request $request, Response $response)
    {

        $collectionData = $this->getCollection($request);
        $types = $this->parseCollection($collectionData);

        $service = new GraphQLService($this->container);
        $responseData = $service->generateSchema($types);
        return $this->responseWithData($request, $response, $responseData);

    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getCollection(Request $request){
        $params = $request->getQueryParams();
        $service = new TablesService($this->container);
        return $service->findAll($params);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function parseCollection(array $data){
        $types = [];
        foreach($data['data'] as $k => $value){
            if( ! $value['single'] && $value['managed']){
                $temp = [];
                $temp['collection'] = $value['collection'];
                foreach($value['fields'] as $field){
                    switch ($field['interface']){
                        case 'primary-key':
                            $temp['fields'][$field['field']] = 'ID';
                        break;
                        case 'text-input':
                            $temp['fields'][$field['field']] = 'String';
                        break;
                        case 'encrypted':
                            $temp['fields'][$field['field']] = 'String';
                        break;
                        case 'numeric':
                            if($field['type'] === 'decimal'){
                                $temp['fields'][$field['field']] = 'Float';
                            }elseif($field['type'] === 'integer'){
                                $temp['fields'][$field['field']] = 'Int';
                            }
                        break;
                    }
                    if($field['required']){
                        $temp['fields'][$field['field']] .="!";
                    }
                }
                $types[] = $temp;
            }
        }
        return $types;
    }

}
