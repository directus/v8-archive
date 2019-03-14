<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Application\Route;
use function Directus\regex_numeric_ids;
use Directus\Services\SettingsService;

class Settings extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        $app->post('', [$this, 'create']);
        $app->get('', [$this, 'all']);
        $app->get('/fields', [$this, 'fields']);
        $app->get('/{id:' . regex_numeric_ids()  . '}', [$this, 'read']);
        $app->patch('/{id:' . regex_numeric_ids()  . '}', [$this, 'update']);
        $app->patch('', [$this, 'update']);
        $app->delete('/{id:' . regex_numeric_ids()  . '}', [$this, 'delete']);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);

        $payload = $request->getParsedBody();
        if (isset($payload[0]) && is_array($payload[0])) {
            return $this->batch($request, $response);
        }

        $service = new SettingsService($this->container);
        $responseData = $service->create(
            $request->getParsedBody(),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function all(Request $request, Response $response)
    {
        $service = new SettingsService($this->container);
        $responseData = $service->findAll(
            $request->getQueryParams()
        );

        /**
         * Get all the fields of settings table to check the interface
         * 
         */

        $fieldData = $service->findAllFields(
            $request->getQueryParams()
        );

        /**
         * Get the 'field' from fields table which contains the file interface for check 
         * from settings table to get the file object ID
         * 
         */
        $fileObject = array_filter($fieldData['data'], function ($value) {
           return $value['interface'] == 'file';
        });

        $fileField = array_column($fileObject, 'field');

        /**
         * Track the setting object and compare the 'key' with 'field'(fileField variable here)
         * which is return by fields table (contains file interface) and if compare successfully 
         * then get the value (which is the ID of files table) to fetch the file object then replace 
         * the "value" of that "key"with this file object.
         * 
         */

        foreach($fileField as $key => $value){
            $result = array_search($value, array_column($responseData['data'], 'key'));
            if($result){
                $fileInstence = $service->findFile($responseData['data'][$result]['value']);
                $responseData['data'][$result]['value'] = !empty($fileInstence['data']) ? $fileInstence['data'] : null;
            }
         }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function fields(Request $request, Response $response)
    {
        $service = new SettingsService($this->container);
        $responseData = $service->findAllFields(
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function read(Request $request, Response $response)
    {
        $service = new SettingsService($this->container);
        $responseData = $service->findByIds(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function update(Request $request, Response $response)
    {
        $this->validateRequestPayload($request);

        $payload = $request->getParsedBody();
        $id = $request->getAttribute('id');

        if (strpos($id, ',') !== false || (isset($payload[0]) && is_array($payload[0]))) {
            return $this->batch($request, $response);
        }

        $inputData = $request->getParsedBody();
        $service = new SettingsService($this->container);

        /**
         * Get the object of current setting from its setting to check the interface.
         * We need to check the interface because for file component (logo); 
         * the request has an array instead of string. So we need to interface of the 
         * given setting object.
         */        
        $isFileRequest = false;
        $serviceData = $service->findByIds(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );


        /**
         * If the current settinghas the file interface then get the id from array
         * for that first get all the fields of settings table to check the interface
         * to get all the file object. 
         * 
         */
       

        $fieldData = $service->findAllFields(
            $request->getQueryParams()
        );

        /**
         * Get the field which contains the file interface
         * 
         */
        $fileObject = array_filter($fieldData['data'], function ($value) use ($serviceData) {
           return $value['interface'] == 'file' && $value['field'] == $serviceData['data']['key'];
        });

        $fileField = array_column($fileObject, 'field');

        $isFileRequest = false;
        if($fileField){
            $isFileRequest =  $inputData['value'];
            $inputData['value'] = isset($inputData['value']['id']) ? $inputData['value']['id'] : $inputData['value'];
        }
        
        $responseData = $service->update(
            $request->getAttribute('id'),
            $inputData,
            $request->getQueryParams()
        );

        /**
         * If the updated setting is a file component then return the object of file 
         * instead of only value.
         */

         if($isFileRequest){
            $responseData['data']['value'] = $isFileRequest;
         }

        return $this->responseWithData($request, $response, $responseData);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function delete(Request $request, Response $response)
    {
        $service = new SettingsService($this->container);

        $id = $request->getAttribute('id');
        if (strpos($id, ',') !== false) {
            return $this->batch($request, $response);
        }

        $service->delete(
            $request->getAttribute('id'),
            $request->getQueryParams()
        );

        return $this->responseWithData($request, $response, []);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    protected function batch(Request $request, Response $response)
    {
        $settingsService = new SettingsService($this->container);

        $payload = $request->getParsedBody();
        $params = $request->getQueryParams();

        $responseData = null;
        if ($request->isPost()) {
            $responseData = $settingsService->batchCreate($payload, $params);
        } else if ($request->isPatch()) {
            $responseData = $settingsService->batchUpdate($payload, $params);
        } else if ($request->isDelete()) {
            $ids = explode(',', $request->getAttribute('id'));
            $settingsService->batchDeleteWithIds($ids, $params);
        }

        return $this->responseWithData($request, $response, $responseData);
    }
}
