# DRCTSSettingsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getSettings**](DRCTSSettingsApi.md#getsettings) | **GET** /settings | Returns settings
[**getSettingsFor**](DRCTSSettingsApi.md#getsettingsfor) | **GET** /settings/{collectionName} | Returns settings for collection
[**updateSettings**](DRCTSSettingsApi.md#updatesettings) | **PUT** /settings/{collectionName} | Update settings


# **getSettings**
```objc
-(NSURLSessionTask*) getSettingsWithCompletionHandler: 
        (void (^)(DRCTSGetSettings* output, NSError* error)) handler;
```

Returns settings

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSSettingsApi*apiInstance = [[DRCTSSettingsApi alloc] init];

// Returns settings
[apiInstance getSettingsWithCompletionHandler: 
          ^(DRCTSGetSettings* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSSettingsApi->getSettings: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetSettings***](DRCTSGetSettings.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getSettingsFor**
```objc
-(NSURLSessionTask*) getSettingsForWithCollectionName: (NSNumber*) collectionName
        completionHandler: (void (^)(DRCTSGetSettingsFor* output, NSError* error)) handler;
```

Returns settings for collection

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSNumber* collectionName = @56; // Name of collection to return settings for

DRCTSSettingsApi*apiInstance = [[DRCTSSettingsApi alloc] init];

// Returns settings for collection
[apiInstance getSettingsForWithCollectionName:collectionName
          completionHandler: ^(DRCTSGetSettingsFor* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSSettingsApi->getSettingsFor: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **NSNumber***| Name of collection to return settings for | 

### Return type

[**DRCTSGetSettingsFor***](DRCTSGetSettingsFor.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **updateSettings**
```objc
-(NSURLSessionTask*) updateSettingsWithCollectionName: (NSNumber*) collectionName
    customData: (NSString*) customData
        completionHandler: (void (^)(NSError* error)) handler;
```

Update settings

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSNumber* collectionName = @56; // Name of collection to return settings for
NSString* customData = customData_example; // Data based on your specific schema eg: active=1&title=LoremIpsum

DRCTSSettingsApi*apiInstance = [[DRCTSSettingsApi alloc] init];

// Update settings
[apiInstance updateSettingsWithCollectionName:collectionName
              customData:customData
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSSettingsApi->updateSettings: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **collectionName** | **NSNumber***| Name of collection to return settings for | 
 **customData** | **NSString***| Data based on your specific schema eg: active&#x3D;1&amp;title&#x3D;LoremIpsum | 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

