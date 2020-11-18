# DRCTSActivityApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getActivity**](DRCTSActivityApi.md#getactivity) | **GET** /activity | Returns activity


# **getActivity**
```objc
-(NSURLSessionTask*) getActivityWithCompletionHandler: 
        (void (^)(DRCTSGetActivity* output, NSError* error)) handler;
```

Returns activity

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSActivityApi*apiInstance = [[DRCTSActivityApi alloc] init];

// Returns activity
[apiInstance getActivityWithCompletionHandler: 
          ^(DRCTSGetActivity* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSActivityApi->getActivity: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetActivity***](DRCTSGetActivity.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

