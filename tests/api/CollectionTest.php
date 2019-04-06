<?php
use Directus\Api\Routes\Auth as Auth;
use GuzzleHttp\Client;
class CollectionTest extends PHPUnit_Framework_TestCase
{
    private $http,$token;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost/directus-api/public/_/',
            'exceptions' => false
        ]);
        
        //Get token
        $data = [
            'form_params' => [
                'email' => "admin@example.com",
                'password' => "password"
            ]
        ];
        
        $response = $this->http->request('POST', 'auth/authenticate', $data);
        $data = json_decode($response->getBody(true), true);
        $this->token = $data['data']['token'];
        
    }

    public function tearDown() {
        $this->http = null;
    }
    
    public function testCreateCollection()
    {
        $data = [
            'headers' => ['Authorization' => 'bearer '.$this->token],
            'form_params' => json_decode('{
                    "collection": "test111_collection",
                    "hidden": 0,
                    "fields": [{
                        "type": "integer",
                        "datatype": "INT",
                        "length": 15,
                        "field": "id",
                        "interface": "primary-key",
                        "auto_increment": true,
                        "primary_key": true,
                        "hidden_detail": true,
                        "hidden_browse": true
                    }, {
                        "type": "status",
                        "datatype": "VARCHAR",
                        "length": 20,
                        "field": "status",
                        "interface": "status",
                        "options": {
                            "status_mapping": {
                                "published": {
                                    "name": "Published",
                                    "text_color": "white",
                                    "background_color": "accent",
                                    "browse_subdued": false,
                                    "browse_badge": true,
                                    "soft_delete": false,
                                    "published": true
                                },
                                "draft": {
                                    "name": "Draft",
                                    "text_color": "white",
                                    "background_color": "blue-grey-200",
                                    "browse_subdued": true,
                                    "browse_badge": true,
                                    "soft_delete": false,
                                    "published": false
                                },
                                "deleted": {
                                    "name": "Deleted",
                                    "text_color": "white",
                                    "background_color": "red",
                                    "browse_subdued": true,
                                    "browse_badge": true,
                                    "soft_delete": true,
                                    "published": false
                                }
                            }
                        }
                    }, {
                        "type": "sort",
                        "datatype": "INT",
                        "field": "sort",
                        "interface": "sort"
                    }, {
                        "type": "user_created",
                        "datatype": "INT",
                        "field": "created_by",
                        "interface": "user-created",
                        "options": {
                                "template": "{{first_name}} {{last_name}}",
                                "display": "both"
                        },
                        "readonly": true,
                        "hidden_detail": true,
                        "hidden_browse": true
                    }, {
                        "type": "datetime_created",
                        "datatype": "DATETIME",
                        "field": "created_on",
                        "interface": "datetime-created",
                        "readonly": true,
                        "hidden_detail": true,
                        "hidden_browse": true
                    }, {
                        "type": "user_updated",
                        "datatype": "INT",
                        "field": "modified_by",
                        "interface": "user-updated",
                        "options": {
                            "template": "{{first_name}} {{last_name}}",
                            "display": "both"
                        },
                        "readonly": true,
                        "hidden_detail": true,
                        "hidden_browse": true
                    }, {
                        "type": "datetime_updated",
                        "datatype": "DATETIME",
                        "field": "modified_on",
                        "interface": "datetime-updated",
                        "readonly": true,
                        "hidden_detail": true,
                        "hidden_browse": true
                    }]
                }',true)
        ];
        
        //echo "<pre>";
        //print_r($data);exit;
        $response = $this->http->request('POST', 'collections', $data);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('collection', $data['data']);
        $this->assertTrue(!empty($data['data']['collection']));
    }
    
    public function testDeleteCollection()
    {
        $data = [
            'headers' => ['Authorization' => 'bearer '.$this->token],
        ];
        
        $response = $this->http->request('DELETE', 'collections/test111_collection', $data);
        
        $this->assertEquals(204, $response->getStatusCode());        
    }
    
}