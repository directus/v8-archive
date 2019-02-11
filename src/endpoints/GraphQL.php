<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use Directus\Services\TablesService;
use Directus\Services\GraphQLService;

class GraphQL extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->get('/', [$this, 'index']); // Used for to access the graphql schema

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

        $collectionData = $this->getCollection($request);
        $types = $this->parseCollection($collectionData);

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
