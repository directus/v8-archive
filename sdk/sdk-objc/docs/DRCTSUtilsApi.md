# DRCTSUtilsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**getHash**](DRCTSUtilsApi.md#gethash) | **POST** /hash | Get a hashed value
[**getRandom**](DRCTSUtilsApi.md#getrandom) | **POST** /random | Returns random alphanumeric string


# **getHash**
```objc
-(NSURLSessionTask*) getHashWithString: (NSString*) string
    hasher: (NSString*) hasher
    options: (NSString*) options
        completionHandler: (void (^)(NSError* error)) handler;
```

Get a hashed value

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* string = @"string_example"; // The string to be hashed (optional)
NSString* hasher = @"core"; // The hasher used to hash the given string (optional) (default to core)
NSString* options = @"options_example"; // The hasher options (optional)

DRCTSUtilsApi*apiInstance = [[DRCTSUtilsApi alloc] init];

// Get a hashed value
[apiInstance getHashWithString:string
              hasher:hasher
              options:options
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSUtilsApi->getHash: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **string** | **NSString***| The string to be hashed | [optional] 
 **hasher** | **NSString***| The hasher used to hash the given string | [optional] [default to core]
 **options** | **NSString***| The hasher options | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

# **getRandom**
```objc
-(NSURLSessionTask*) getRandomWithLength: (NSString*) length
        completionHandler: (void (^)(NSError* error)) handler;
```

Returns random alphanumeric string

### Example 
```objc
DRCTSDefaultConfiguration *apiConfig = [DRCTSDefaultConfiguration sharedConfig];

// Configure API key authorization: (authentication scheme: api_key)
[apiConfig setApiKey:@"YOUR_API_KEY" forApiKeyIdentifier:@"access_token"];
// Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
//[apiConfig setApiKeyPrefix:@"Bearer" forApiKeyIdentifier:@"access_token"];


NSString* length = @"length_example"; // Integer(String) for length of random string (optional)

DRCTSUtilsApi*apiInstance = [[DRCTSUtilsApi alloc] init];

// Returns random alphanumeric string
[apiInstance getRandomWithLength:length
          completionHandler: ^(NSError* error) {
                        if (error) {
                            NSLog(@"Error calling DRCTSUtilsApi->getRandom: %@", error);
                        }
                    }];
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **length** | **NSString***| Integer(String) for length of random string | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

