# DRCTSPreferencesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getPreferences**](DRCTSPreferencesApi.md#getpreferences) | **GET** /tables/{tableId}/preferences | Returns table preferences
[**updatePreferences**](DRCTSPreferencesApi.md#updatepreferences) | **PUT** /tables/{tableId}/preferences | Update table preferences


# **getPreferences**
```objc
-(NSURLSessionTask*) getPreferencesWithTableId: (NSString*) tableId
        completionHandler: (void (^)(DRCTSGetPreferences* output, NSError* error)) handler;
```

Returns table preferences

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from

DRCTSPreferencesApi*apiInstance = [[DRCTSPreferencesApi alloc] init];

// Returns table preferences
[apiInstance getPreferencesWithTableId:tableId
          completionHandler: ^(DRCTSGetPreferences* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSPreferencesApi->getPreferences: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 

### Return type

[**DRCTSGetPreferences***](DRCTSGetPreferences.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **updatePreferences**
```objc
-(NSURLSessionTask*) updatePreferencesWithTableId: (NSString*) tableId
    _id: (NSString*) _id
    tableName: (NSString*) tableName
    columnsVisible: (NSString*) columnsVisible
    sort: (NSNumber*) sort
    sortOrder: (NSString*) sortOrder
    status: (NSString*) status
        completionHandler: (void (^)(NSError* error)) handler;
```

Update table preferences

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* tableId = @"tableId_example"; // ID of table to return rows from
NSString* _id = @"_id_example"; // Preference's Unique Identification number (optional)
NSString* tableName = @"tableName_example"; // Name of table to add (optional)
NSString* columnsVisible = @"columnsVisible_example"; // List of visible columns, separated by commas (optional)
NSNumber* sort = @56; // The sort order of the column used to override the column order in the schema (optional)
NSString* sortOrder = @"sortOrder_example"; // Sort Order (ASC=Ascending or DESC=Descending) (optional)
NSString* status = @"status_example"; // List of status values. separated by comma (optional)

DRCTSPreferencesApi*apiInstance = [[DRCTSPreferencesApi alloc] init];

// Update table preferences
[apiInstance updatePreferencesWithTableId:tableId
              _id:_id
              tableName:tableName
              columnsVisible:columnsVisible
              sort:sort
              sortOrder:sortOrder
              status:status
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSPreferencesApi->updatePreferences: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **tableId** | **NSString***| ID of table to return rows from | 
 **_id** | **NSString***| Preference&#39;s Unique Identification number | [optional] 
 **tableName** | **NSString***| Name of table to add | [optional] 
 **columnsVisible** | **NSString***| List of visible columns, separated by commas | [optional] 
 **sort** | **NSNumber***| The sort order of the column used to override the column order in the schema | [optional] 
 **sortOrder** | **NSString***| Sort Order (ASC&#x3D;Ascending or DESC&#x3D;Descending) | [optional] 
 **status** | **NSString***| List of status values. separated by comma | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

