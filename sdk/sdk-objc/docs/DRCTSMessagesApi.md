# DRCTSMessagesApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getMessage**](DRCTSMessagesApi.md#getmessage) | **GET** /messages/{messageId} | Returns specific message
[**getMessages**](DRCTSMessagesApi.md#getmessages) | **GET** /messages/self | Returns messages


# **getMessage**
```objc
-(NSURLSessionTask*) getMessageWithMessageId: (NSNumber*) messageId
        completionHandler: (void (^)(DRCTSGetMessage* output, NSError* error)) handler;
```

Returns specific message

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSNumber* messageId = @56; // ID of message to return

DRCTSMessagesApi*apiInstance = [[DRCTSMessagesApi alloc] init];

// Returns specific message
[apiInstance getMessageWithMessageId:messageId
          completionHandler: ^(DRCTSGetMessage* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSMessagesApi->getMessage: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **messageId** | **NSNumber***| ID of message to return | 

### Return type

[**DRCTSGetMessage***](DRCTSGetMessage.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getMessages**
```objc
-(NSURLSessionTask*) getMessagesWithCompletionHandler: 
        (void (^)(DRCTSGetMessages* output, NSError* error)) handler;
```

Returns messages

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];



DRCTSMessagesApi*apiInstance = [[DRCTSMessagesApi alloc] init];

// Returns messages
[apiInstance getMessagesWithCompletionHandler: 
          ^(DRCTSGetMessages* output, NSError* error) {
                        if (output) {
                            NSLog(@"%@", output);
                        }
                        if (error) {
                            NSLog(@"Error calling DRCTSMessagesApi->getMessages: %@", error);
                        }
                    }];
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**DRCTSGetMessages***](DRCTSGetMessages.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

